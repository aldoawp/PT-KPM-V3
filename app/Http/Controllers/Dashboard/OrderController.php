<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\Dashboard\Enums\Carts;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = [];

        $userRole = auth()->user()->role->name;

        if ($userRole === 'SuperAdmin' || $userRole === 'Owner') {
            $orders = Order::where('order_status', 'pending');
        } else {
            $orders =
                Order::where('order_status', 'pending')->where('branch_id', auth()->user()->branch->id);
        }

        return view('orders.pending-orders', [
            'orders' => $orders->sortable()->paginate($row)
        ]);
    }

    public function completeOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = [];

        $userRole = auth()->user()->role->name;

        if ($userRole === 'SuperAdmin' || $userRole === 'Owner') {
            $orders = Order::where('order_status', 'complete');
        } else {
            $orders =
                Order::where('order_status', 'complete')->where('branch_id', auth()->user()->branch->id);
        }

        return view('orders.complete-orders', [
            'orders' => $orders->sortable()->paginate($row)
        ]);
    }

    public function stockManage()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('stock.index', [
            'products' => Product::with(['category', 'supplier'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrder(Request $request)
    {
        $cart = Cart::instance(Carts::Sales->value);

        $validator =
            \Validator::make($request->all(), [
                'payment_status' => ['required', 'in:tunai,cek,bon'],
                'pay' => ['required', 'numeric', 'min:0', 'max:' . $cart->total()]
            ]);

        if ($validator->fails()) {
            return redirect()->route('pos.salesPos');
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
                'payment_status' => $request['payment_status'],
                'pay' => $request['pay'],
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
        }

        $cart->destroy();

        // return Redirect::route('dashboard')->with('success', 'Order has been created!');
        return Redirect::route('order.orderDetails', $order->id)->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Remove the specified resource from database.
     */
    public function deleteOrder(Request $request, int $order_id)
    {
        $order =
            Order::find($order_id);

        if (explode('/', $request->path())[1] === 'complete') {
            // Add product stock back
            $orderDetails = $order->orderDetails;

            foreach ($orderDetails as $orderDetail) {
                $product = Product::find($orderDetail->product_id);
                $product->product_store += $orderDetail->quantity;
                $product->save();
            }
        }

        $order->delete();

        return Redirect::back()->with('success', 'Pesanan berhasil dihapus!');
    }

    /**
     * Display the specified resource.
     */
    public function orderDetails(int $order_id)
    {
        $order = Order::find($order_id);
        $orderDetails = $order->orderDetails;

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request)
    {
        $order_id = $request->id;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['product_store' => DB::raw('product_store-' . $product->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'complete']);

        return Redirect::route('order.viewReceipt', $order_id);
    }

    public function viewReceipt(int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('order_id', 'DESC')
            ->get();

        return view('orders.receipt-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function invoiceDownload(int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('order_id', 'DESC')
            ->get();

        // show data (only for debugging)
        return view('orders.invoice-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function pendingDue()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('due', '>', '0')
            ->sortable()
            ->paginate($row);

        return view('orders.pending-due', [
            'orders' => $orders
        ]);
    }

    public function orderDueAjax(int $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function updateDue(Request $request)
    {
        $rules = [
            'order_id' => 'required|numeric',
            'due' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        $order = Order::findOrFail($request->order_id);
        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paid_due = $mainDue - $validatedData['due'];
        $paid_pay = $mainPay + $validatedData['due'];

        Order::findOrFail($request->order_id)->update([
            'due' => $paid_due,
            'pay' => $paid_pay,
        ]);

        return Redirect::route('order.pendingDue')->with('success', 'Due Amount Updated Successfully!');
    }
}
