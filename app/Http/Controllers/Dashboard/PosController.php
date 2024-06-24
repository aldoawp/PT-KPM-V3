<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Returns;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;

enum Carts: string
{
    case Sales = 'cart-sales';
    case Restock = 'cart-restock';
    case Return = 'cart-return';

    public static function getFromPath($path)
    {
        $path = explode('/', $path)[1];

        return match ($path) {
            'sales' => self::Sales,
            'restock' => self::Restock,
            'return' => self::Return ,
        };
    }
}

class PosController extends Controller
{
    public function posSales(Request $request)
    {
        $todayDate = Carbon::now();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $customers = [];
        $products = [];

        $userRole = auth()->user()->role->name;

        if ($userRole === 'SuperAdmin' || $userRole === 'Owner') {
            $customers = Customer::all();
            $products = Product::where('expire_date', '>', $todayDate);
        } else {
            $customers = auth()->user()->branch->customers;
            $products = auth()->user()->branch->products()->where('expire_date', '>', $todayDate);
        }

        return view('pos.sales-pos', [
            'customers' => $customers->sortBy('name'),
            'productItem' => Cart::instance(Carts::Sales->value),
            'products' => $products->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function posRestock(Request $request)
    {
        $todayDate = Carbon::now();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = [];

        $userRole = auth()->user()->role->name;

        if ($userRole === 'SuperAdmin' || $userRole === 'Owner') {
            $products = Product::where('expire_date', '>', $todayDate);
        } else {
            $products = auth()->user()->branch->products()->where('expire_date', '>', $todayDate);
        }

        return view('pos.restock-pos', [
            'suppliers' => Supplier::all()->sortBy('name'),
            'productItem' => Cart::instance(Carts::Restock->value),
            'products' => $products->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function posReturn(Request $request)
    {
        $todayDate = Carbon::now();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = [];

        $userRole = auth()->user()->role->name;

        if ($userRole === 'SuperAdmin' || $userRole === 'Owner') {
            $products = Product::where('expire_date', '>', $todayDate);
        } else {
            $products = auth()->user()->branch->products()->where('expire_date', '>', $todayDate);
        }

        return view('pos.return-pos', [
            'suppliers' => Supplier::all()->sortBy('name'),
            'productItem' => Cart::instance(Carts::Return ->value),
            'products' => $products->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function addCart(Request $request)
    {
        // Check if path is restock path
        if (explode('/', $request->path())[1] !== 'restock') {
            // Validate if prouct stock not 0
            $product = Product::find($request['id']);

            if ($product->product_store === 0) {
                return Redirect::back()->withErrors(['error' => 'Produk tidak tersedia!']);
            }
        }

        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::getFromPath($request->path())->value)->add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Produk telah ditambahkan!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::getFromPath($request->path())->value)
            ->update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Keranjang telah diperbarui!');
    }

    public function deleteCart(Request $request, $rowId)
    {
        Cart::instance(Carts::getFromPath($request->path())->value)
            ->remove($rowId);

        return Redirect::back()->with('success', 'Keranjang telah dihapus!');
    }

    public function createOrder(Request $request)
    {
        $path = explode('/', $request->path());

        $cartName = Carts::getFromPath($request->path())->value;

        if ($path[1] === 'sales') {
            $cart = Cart::instance($cartName);

            if ($cart->count() === 0) {
                return redirect()->back()->withErrors(['error' => 'Tambahkan setidaknya 1 barang!']);
            }

            $validator =
                \Validator::make($request->all(), [
                    'customer_id' => ['required']
                ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors(['error' => 'Pilih 1 pelanggan!']);
            }

            $invoice_no = IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]);

            $order =
                Order::create([
                    'customer_id' => $request['customer_id'],
                    'total_products' => $cart->count(),
                    'sub_total' => $cart->subtotal(),
                    'vat' => $cart->tax(),
                    'invoice_no' => $invoice_no,
                    'total' => $cart->total(),
                    'due' => $cart->total() - $request['pay'],
                    'branch_id' => auth()->user()->branch->id,
                    'user_id' => auth()->user()->id,
                ]);

            foreach ($cart->content() as $item) {
                $order->orderDetails()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->qty,
                    'unitcost' => $item->price,
                    'total' => $item->total
                ]);

                // Update stock
                $product = Product::find($item->id);
                $product->product_store -= $item->qty;

                $product->save();
            }
        } else { // Uses supplier
            $cart = Cart::instance($cartName);

            if ($cart->count() === 0) {
                return redirect()->back()->withErrors(['error' => 'Tambahkan setidaknya 1 barang!']);
            }

            $validator =
                \Validator::make($request->all(), [
                    'supplier_id' => ['required']
                ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors(['error' => 'Pilih 1 pemasok!']);
            }

            // Update product quantity & create record
            if ($path[1] === 'restock') {
                $restockRecord =
                    Restock::create([
                        'supplier_id' => $request['supplier_id'],
                        'branch_id' => auth()->user()->branch->id,
                        'total' => $cart->total(),
                        'user_id' => auth()->user()->id
                    ]);

                foreach ($cart->content() as $item) {
                    $restockRecord->restockDetails()
                        ->create([
                            'product_id' => $item->id,
                            'quantity' => $item->qty
                        ]);

                    // Update stock
                    $product = Product::find($item->id);
                    $product->product_store += $item->qty;

                    $product->save();
                }
            } else {
                $returnsRecord =
                    Returns::create([
                        'supplier_id' => $request['supplier_id'],
                        'branch_id' => auth()->user()->branch->id,
                        'total' => $cart->total(),
                        'user_id' => auth()->user()->id
                    ]);

                foreach ($cart->content() as $item) {
                    $returnsRecord->returnDetails()
                        ->create([
                            'product_id' => $item->id,
                            'quantity' => $item->qty
                        ]);

                    // Update stock
                    $product = Product::find($item->id);
                    $product->product_store -= $item->qty;

                    $product->save();
                }
            }
        }

        $cart->destroy();

        return redirect()->back()->with('success', 'Pesanan berhasil dibuat!');
    }
}
