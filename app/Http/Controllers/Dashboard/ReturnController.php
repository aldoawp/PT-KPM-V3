<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Returns;
use App\Models\ReturnDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class ReturnController extends Controller
{
    public function storeOrder(Request $request)
    {
        $rules = [
            'supplier_id' => 'required|numeric',
        ];

        $cart = Cart::instance(Carts::Return ->value);

        $validatedData = $request->validate($rules);
        $validatedData['total'] = $cart->total();
        $validatedData['branch_id'] = auth()->user()->branch->id;
        $validatedData['user_id'] = auth()->user()->id;

        $return_id = Returns::insertGetId($validatedData);

        // Create Order Details
        $contents = $cart->content();
        $oDetails = array();

        foreach ($contents as $content) {
            $oDetails['return_id'] = $return_id;
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;

            ReturnDetail::insert($oDetails);
        }

        // Delete Cart Sopping History
        $cart->destroy();

        return Redirect::route('pos.returnPos', $return_id)->with('success', 'Pesanan berhasil dibuat!');
    }
}
