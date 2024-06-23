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
        // $income_weekly = Order::whereBetween('created_at', [now()->subWeek(), now()])->sum('total');

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

        /*
         Income per location algorithm:
         1. Take the data from orders table
         2. From orders table, join it with users table using user_id as the foreign key
         3. Then, join the users table with branches table using branch_id as the foreign key
         4. After that, group by branch_id and sum up the total amount per location from the 'total' column on orders table, where 'order_status' is 'complete'
         5. Take the region name from branches table and the sum of each region based on branch_id
         */


        $income_per_location = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->select('branches.region', DB::raw('SUM(orders.total) as total_income'))
            ->where('orders.order_status', 'complete')
            ->groupBy('orders.user_id', 'branches.region')
            ->get()
            ->map(function ($income) {
                return [
                    'region' => $income->region,
                    'income' => $income->total_income
                ];
            });

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

        // Top sales
        $top_sales = DB::table('orders')
            ->select('users.name', DB::raw('SUM(orders.total) as total_sales'), DB::raw('SUM(orders.total_products) as total_products'))
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_status', 'complete')
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'today_income' => $today_income,
            'today_product' => $today_product,
            'today_complete_orders' => $today_complete_orders,
            'best_sellers' => $best_sellers,
            'new_products' => Product::orderBy('buying_date')->take(2)->get(),
            'income_weekly' => $income_weekly,
            'income_total' => $income_total,
            'top_sales' => $top_sales,
            'income_per_location' => $income_per_location,
        ]);
    }
}
