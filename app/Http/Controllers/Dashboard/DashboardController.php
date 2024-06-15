<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $income_weekly = Order::whereBetween('created_at', [now()->subWeek(), now()])->sum('total');

        $income_weekly = Order::where('order_status', 'complete')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->get()
            ->map(function ($income) {
                return [
                    'date' => Carbon::parse($income->date)->toIso8601String(),
                    'total' => $income->total
                ];
            });

        $income_total = [
            'total' => Order::where('order_status', 'complete')->sum('total')
        ];

        // Today's insights
        $today_income = Order::whereDate('created_at', Carbon::now())->sum('total');
        $today_product = Order::whereDate('created_at', Carbon::now())->sum('total_products');
        $today_complete_orders = Order::whereDate('created_at', Carbon::now())->where('order_status', 'complete')->get();

        // Product best seller
        $best_sellers = DB::table('order_details')
            ->select('products.product_name', 'categories.name as category_name', 'products.product_image', DB::raw('SUM(order_details.quantity) as total_quantity'))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('order_details.product_id', 'products.product_name', 'products.product_image', 'categories.name')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();


        // $product_qty = OrderDetails::selectRaw('product_id, SUM(quantity) as qty')->groupBy('product_id')->orderBy('qty', 'desc')->take(5)->get();


        return view('dashboard.index', [
            'today_income' => $today_income,
            'today_product' => $today_product,
            'today_complete_orders' => $today_complete_orders,
            'best_sellers' => $best_sellers,
            'new_products' => Product::orderBy('buying_date')->take(2)->get(),
            'income_weekly' => $income_weekly,
            'income_total' => $income_total
        ]);
    }
}
