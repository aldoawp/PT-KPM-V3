<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use App\Models\Restock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Controllers\Dashboard\Enums\Carts;

class RestockController extends Controller
{
    public function storeOrder(Request $request)
    {
        $cart = Cart::instance(Carts::Restock->value);

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

        $cart->destroy();

        return redirect()->back()->with('success', 'Pesanan berhasil dibuat!');
    }
}
