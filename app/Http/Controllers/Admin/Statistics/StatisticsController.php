<?php

namespace App\Http\Controllers\Admin\Statistics;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Lọc theo ngày tháng nếu có request
        $date = $request->input('date');
        
        $orderQuery = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Hoàn thành')
                  ->where('payment_status', 'Đã thanh toán');
        });

        if ($date) {
            $orderQuery->whereDate('created_at', $date);
        }

        // Thống kê tổng quan
        $totalProducts = Product::count();
        $totalCategory = Category::count();
        $totalStock = ProductVariant::sum('stock_quantity');
        $totalSold = ProductVariant::sum('sold_quantity');

        // Thống kê doanh thu
        $totalRevenue = $orderQuery->selectRaw('SUM(quantity * price) as revenue')->first()->revenue ?? 0;
        $totalSoldFiltered = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Hoàn thành')
            ->where('payment_status', 'Đã thanh toán');
        })->sum('quantity');
        
        $revenueByCategory = Category::with(['children', 'children.products.variants' => function ($query) {
            $query->selectRaw('product_id, SUM(sold_quantity * price) as revenue')->groupBy('product_id');
        }, 'products.variants' => function ($query) {
            $query->selectRaw('product_id, SUM(sold_quantity * price) as revenue')->groupBy('product_id');
        }])->whereNull('parent_id')->get();

        // Tính tổng doanh thu của danh mục cha từ chính nó và danh mục con
        foreach ($revenueByCategory as $category) {
            $category->own_revenue = $category->products->sum(function ($product) {
                return $product->variants->sum('revenue');
            });
            $category->children_revenue = $category->children->sum(function ($child) {
                return $child->products->sum(function ($product) {
                    return $product->variants->sum('revenue');
                });
            });
            $category->total_revenue = $category->own_revenue + $category->children_revenue;
        }

        $revenueByProduct = Product::with(['variants' => function ($query) {
            $query->selectRaw('product_id, SUM(sold_quantity * price) as revenue')->groupBy('product_id');
        }])->get();

        // Thống kê theo thời gian
        $revenueByDay = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Hoàn thành')->where('payment_status', 'Đã thanh toán');
        })->whereDate('created_at', Carbon::today())->sum(\DB::raw('quantity * price'));

        $revenueByMonth = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Hoàn thành')->where('payment_status', 'Đã thanh toán');
        })->whereMonth('created_at', Carbon::now()->month)->sum(\DB::raw('quantity * price'));

        $revenueByYear = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Hoàn thành')->where('payment_status', 'Đã thanh toán');
        })->whereYear('created_at', Carbon::now()->year)->sum(\DB::raw('quantity * price'));

        // **Thống kê sản phẩm bán chạy**
        $topSellingProducts = Product::select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
        ->join('order_items', 'products.id', '=', 'order_items.product_id') // Đổi từ product_variant_id thành product_id
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.status', 'Hoàn thành')
        ->where('orders.payment_status', 'Đã thanh toán')
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_sold')
        ->take(10)
        ->get();
    

        return view('Admin.statistics.index', compact(
            'totalProducts', 'totalCategory', 'totalStock', 'totalSold',
            'totalRevenue', 'revenueByCategory', 'revenueByProduct',
            'revenueByDay', 'revenueByMonth', 'revenueByYear',
            'totalSoldFiltered', 'topSellingProducts'
        ));
    }
}
