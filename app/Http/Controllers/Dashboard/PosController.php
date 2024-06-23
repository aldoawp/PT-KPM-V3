<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Restock;
use App\Models\Returns;
use App\Models\Supplier;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

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
            $products = auth()->user()->branch->products->where('expire_date', '>', $todayDate);
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
            $products = auth()->user()->branch->products->where('expire_date', '>', $todayDate);
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
            $products = auth()->user()->branch->products->where('expire_date', '>', $todayDate);
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

    public function createInvoice(Request $request)
    {
        $path = explode('/', $request->path());

        $cartName = Carts::getFromPath($request->path())->value;

        if ($path[1] === 'sales') {
            $cart =
                Cart::instance($cartName);

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

            $customer =
                Customer::find($request['customer_id']);

            // Update product quantity
            foreach ($cart->content() as $item) {
                $product = Product::find($item->id);
                $product->product_store += $item->qty;

                $product->save();
            }

            Cart::instance($cartName)->destroy();

            return view('pos.create-invoice-customer', [
                'customer' => $customer,
                'productItem' => $cart
            ]);
        } else {
            $cart =
                Cart::instance($cartName);

            if ($cart->count() === 0) {
                return Redirect::back()->withErrors(['error' => 'Tambahkan setidaknya 1 barang!']);
            }

            $validator =
                \Validator::make($request->all(), [
                    'customer_id' => ['required']
                ]);

            if ($validator->fails() || $cart->count() === 0) {
                return Redirect::back()->withErrors(['error' => 'Pilih 1 pemasok!']);
            }

            $supplier =
                Supplier::find($request['supplier_id']);
            $cart = Cart::instance($cartName);

            // Update product quantity
            if ($path[1] === 'restock') {
                $restockRecord = Restock::create([
                    'supplier_id' => $request['supplier_id'],
                    'branch_id' => auth()->user()->branch->id,
                    'total' => Cart::instance($cartName)->total()
                ]);

                foreach ($cart->content() as $item) {
                    $restockRecord->restockDetails()->create([
                        'product_id' => $item->id,
                        'quantity' => $item->qty
                    ]);

                    $product = Product::find($item->id);
                    $product->product_store -= $item->qty;

                    $product->save();
                }
            } else {
                $returnsRecord = Returns::create([
                    'supplier_id' => $request['supplier_id'],
                    'branch_id' => auth()->user()->branch->id,
                    'total' => Cart::instance($cartName)->total()
                ]);

                foreach ($cart->content() as $item) {
                    $returnsRecord->restockDetails()->create([
                        'product_id' => $item->id,
                        'quantity' => $item->qty
                    ]);

                    $product = Product::find($item->id);
                    $product->product_store -= $item->qty;

                    $product->save();
                }
            }

            Cart::instance($cartName)->destroy();

            return view('pos.create-invoice-supplier', [
                'supplier' => $supplier,
                'productItem' => $cart
            ]);
        }
    }

    public function printInvoice(Request $request)
    {
        $path = explode('/', $request->path());

        $cartName = Carts::getFromPath($request->path())->value;

        if ($path[1] === 'sales') {
            $rules = [
                'customer_id' => 'required'
            ];

            $validatedData = $request->validate($rules);
            $customer =
                Customer::where($validatedData['customer_id']);
            $content =
                Cart::instance($cartName)->content();
            $order =
                Customer::find($validatedData['customer_id'])->orders();

            return view('pos.print-invoice', [
                'customer' => $customer,
                'content' => $content,
                'order' => $order
            ]);
        } else {
            $rules = [
                'supplier_id' => 'required'
            ];

            $cartName = Carts::getFromPath($request->path())->value;

            $validatedData = $request->validate($rules);
            $supplier =
                Supplier::where($validatedData['supplier_id']);
            $content =
                Cart::instance($cartName)->content();
            $order =
                Customer::find($validatedData['supplier_id'])->orders();

            return view('pos.print-invoice', [
                'supplier' => $supplier,
                'content' => $content,
                'order' => $order
            ]);
        }
    }
}
