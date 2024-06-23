<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Supplier;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

enum Carts: string
{
    case Sales = 'cart-sales';
    case Restock = 'cart-restock';
    case Return = 'cart-return';
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

    public function addCartSales(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Sales->value)->add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Produk telah ditambahkan!');
    }

    public function addCartRestock(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Restock->value)->add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Produk telah ditambahkan!');
    }

    public function addCartReturn(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Return ->value)->add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Produk telah ditambahkan!');
    }

    public function updateCartSales(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Sales->value)->update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Keranjang telah diperbarui!');
    }

    public function updateCartRestock(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Restock->value)->update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Keranjang telah diperbarui!');
    }

    public function updateCartReturn(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::instance(Carts::Return ->value)->update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Keranjang telah diperbarui!');
    }

    public function deleteCartSales($rowId)
    {
        Cart::instance(Carts::Sales->value)->remove($rowId);

        return Redirect::back()->with('success', 'Keranjang telah dihapus!');
    }

    public function deleteCartRestock($rowId)
    {
        Cart::instance(Carts::Restock->value)->remove($rowId);

        return Redirect::back()->with('success', 'Keranjang telah dihapus!');
    }

    public function deleteCartReturn($rowId)
    {
        Cart::instance(Carts::Return ->value)->remove($rowId);

        return Redirect::back()->with('success', 'Keranjang telah dihapus!');
    }

    public function addProductQuantity($cartName)
    {
        foreach (Cart::instance($cartName)->content() as $item) {
            $product = Product::find($item->id);
            // $product->quantity = $product->quantity + $item->qty;
            $product->save();
        }
    }

    public function subProductQuantity($cartName)
    {
        foreach (Cart::instance($cartName)->content() as $item) {
            $product = Product::find($item->id);
            // $product->quantity = $product->quantity - $item->qty;
            $product->save();
        }
    }

    public function createTransaction()
    {

    }

    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.create-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }

    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();
        $order = Order::where('customer_id', $customer->id)->first();

        return view('pos.print-invoice', [
            'customer' => $customer,
            'content' => $content,
            'order' => $order
        ]);
    }
}
