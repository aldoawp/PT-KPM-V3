<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Restock;
use Illuminate\Http\Request;
use App\Models\RestockDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class RestockController extends Controller
{
    public function storeOrder(Request $request)
    {
        $rules = [
            'supplier_id' => 'required|numeric',
        ];

        $cart = Cart::instance(Carts::Restock->value);

        $validatedData = $request->validate($rules);
        $validatedData['total'] = $cart->total();
        $validatedData['branch_id'] = auth()->user()->branch->id;
        $validatedData['user_id'] = auth()->user()->id;

        $return_id = Restock::insertGetId($validatedData);

        // Create Order Details
        $contents = $cart->content();
        $oDetails = array();

        foreach ($contents as $content) {
            $oDetails['return_id'] = $return_id;
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;

            RestockDetail::insert($oDetails);
        }

        // Delete Cart Sopping History
        $cart->destroy();

        return Redirect::route('pos.restockPos', $return_id)->with('success', 'Pesanan berhasil dibuat!');
    }
}
