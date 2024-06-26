<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Controllers\Dashboard\Enums\Carts;

class PosController extends Controller
{
    public function posSales(Request $request)
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $customers = [];
        $products = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $customers = Customer::all();
            $products = Product::filter(request(['search']));
        } else {
            $customers = auth()->user()->branch->customers;
            $products = auth()->user()->branch->products()->filter(request(['search']));
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
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $products = Product::filter(request(['search']));
        } else {
            $products = auth()->user()->branch->products()->filter(request(['search']));
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
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = [];

        if (auth()->user()->isSuperAdmin() || auth()->user()->isOwner()) {
            $products = Product::filter(request(['search']));
        } else {
            $products = auth()->user()->branch->products()->filter(request(['search']));
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

    public function createInvoice(Request $request)
    {
        $cart = Cart::instance(Carts::Sales->value);

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

        return view('pos.create-invoice', [
            'customer' => $customer,
            'productItem' => $cart
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
