<?php

namespace App\Http\Controllers\Admin\Statistics;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::all();
        $productVariants = ProductVariant::all();
        $orders = Order::all();
        $orderItems = OrderItem::all();

        return view('Admin.statistics.index', compact(
            'categories',
            'products',
            'productVariants',
            'orders',
            'orderItems'
        ));
    }

    public function profitStatistics(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $productName = $request->input('product_name');
        $categoryId = $request->input('category_id');
        $orderId = $request->input('order_id');

        $ordersQuery = Order::where('status', 'Hoàn thành')->with('orderItems');

        if ($from) {
            $ordersQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('created_at', '<=', $to);
        }
        if ($orderId) {
            $ordersQuery->where('id', $orderId);
        }

        $orders = $ordersQuery->get();
        $orderProfits = [];
        $productProfits = [];

        foreach ($orders as $order) {
            $orderRevenue = 0;
            $orderCost = 0;

            foreach ($order->orderItems as $item) {
                $variant = ProductVariant::find($item->variant_id);
                $product = Product::find($item->product_id);
                if (!$variant || !$product) continue;

                if ($productName && stripos($product->name, $productName) === false) {
                    continue;
                }

                if ($categoryId) {
                    $categoryIds = Category::getDescendantsAndSelfIds($categoryId);
                    if (!in_array($product->category_id, $categoryIds)) {
                        continue;
                    }
                }

                $itemRevenue = $item->price * $item->quantity;
                $itemCost = $variant->original_price * $item->quantity;
                $itemProfit = $itemRevenue - $itemCost;

                if (!isset($productProfits[$product->id])) {
                    $productProfits[$product->id] = [
                        'product_name' => $product->name,
                        'total_revenue' => 0,
                        'total_cost' => 0,
                        'total_profit' => 0,
                        'total_sold' => 0,
                    ];
                }

                $productProfits[$product->id]['total_revenue'] += $itemRevenue;
                $productProfits[$product->id]['total_cost'] += $itemCost;
                $productProfits[$product->id]['total_profit'] += $itemProfit;
                $productProfits[$product->id]['total_sold'] += $item->quantity;

                $orderRevenue += $itemRevenue;
                $orderCost += $itemCost;
            }

            if ($orderRevenue > 0 || $orderCost > 0) {
                $orderProfits[] = [
                    'order_id' => $order->id,
                    'order_code' => $order->code ?? ('Mã đơn ' . $order->id),
                    'total_revenue' => $orderRevenue,
                    'total_cost' => $orderCost,
                    'total_profit' => $orderRevenue - $orderCost,
                    'created_at' => $order->created_at,
                ];
            }
        }

        // Thống kê mã giảm giá
        $couponQuery = Coupon::query();
        $usageQuery = CouponUsage::query();
        $orderQuery = Order::where('status', 'Hoàn thành');
        if ($from) {
            $couponQuery->whereDate('created_at', '>=', $from);
            $usageQuery->whereDate('used_at', '>=', $from);
            $orderQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $couponQuery->whereDate('created_at', '<=', $to);
            $usageQuery->whereDate('used_at', '<=', $to);
            $orderQuery->whereDate('created_at', '<=', $to);
        }

        $totalCoupons = $couponQuery->count();
        $activeCoupons = $couponQuery->clone()->where('status', 1)->whereDate('end_date', '>=', Carbon::today())->count();
        $expiredCoupons = $couponQuery->clone()->where(function ($q) {$q->where('status', 2)->orWhereDate('end_date', '<', Carbon::today());})->count();
        $shippingCoupons = $couponQuery->clone()->where('discount_target', 'shipping_fee')->count();
        $orderCoupons = $couponQuery->clone()->where('discount_target', 'order_total')->count();

        $totalUsages = $usageQuery->count();
        $shippingUsages = $usageQuery->clone()->whereHas('coupon', fn($q) => $q->where('discount_target', 'shipping_fee'))->count();
        $orderUsages = $usageQuery->clone()->whereHas('coupon', fn($q) => $q->where('discount_target', 'order_total'))->count();
        $totalDiscountQuery = Order::whereNotNull('coupon_code')->where('status', '!=', 'Đã hủy');
        if ($from) $totalDiscountQuery->whereDate('created_at', '>=', $from);
        if ($to) $totalDiscountQuery->whereDate('created_at', '<=', $to);
        $totalDiscount = $totalDiscountQuery->sum('discount_amount');
        $uniqueUsers = $usageQuery->distinct('user_id')->count('user_id');
        $topUsedCouponsQuery = Coupon::whereHas('couponUsages', function ($query) use ($from, $to) {
            if ($from) $query->whereDate('used_at', '>=', $from);
            if ($to) $query->whereDate('used_at', '<=', $to);
        })->withCount(['couponUsages' => function ($query) use ($from, $to) {
            if ($from) $query->whereDate('used_at', '>=', $from);
            if ($to) $query->whereDate('used_at', '<=', $to);
        }]);
        
        $topUsedCoupons = $topUsedCouponsQuery
            ->orderBy('coupon_usages_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($coupon) use ($from, $to) {
                $usageQuery = CouponUsage::where('coupon_id', $coupon->id);
                if ($from) $usageQuery->whereDate('used_at', '>=', $from);
                if ($to) $usageQuery->whereDate('used_at', '<=', $to);
                $usageCount = $usageQuery->count();
                $totalDiscount = $usageQuery->sum('applied_discount');
                return [
                    'code' => $coupon->code,
                    'type' => $coupon->discount_target == 'shipping_fee' ? 'Phí vận chuyển' : 'Giá trị đơn',
                    'usage_count' => $usageCount,
                    'total_discount' => $totalDiscount,
                ];
        });

        $leastUsedCouponsQuery = Coupon::whereHas('couponUsages', function ($query) use ($from, $to) {
            if ($from) $query->whereDate('used_at', '>=', $from);
            if ($to) $query->whereDate('used_at', '<=', $to);
        })->withCount(['couponUsages' => function ($query) use ($from, $to) {
            if ($from) $query->whereDate('used_at', '>=', $from);
            if ($to) $query->whereDate('used_at', '<=', $to);
        }]);

        $leastUsedCoupons = $leastUsedCouponsQuery
        ->orderBy('coupon_usages_count', 'asc')
        ->take(5)
        ->get()
        ->map(function ($coupon) use ($from, $to) {
            $usageQuery = CouponUsage::where('coupon_id', $coupon->id);
            if ($from) $usageQuery->whereDate('used_at', '>=', $from);
            if ($to) $usageQuery->whereDate('used_at', '<=', $to);
            $usageCount = $usageQuery->count();
            $totalDiscount = $usageQuery->sum('applied_discount');
            return [
                'code' => $coupon->code,
                'type' => $coupon->discount_target == 'shipping_fee' ? 'Phí vận chuyển' : 'Giá trị đơn',
                'usage_count' => $usageCount,
                'total_discount' => $totalDiscount,
            ];
        });

        $totalOrders = $orderQuery->count();
        $couponOrders = $orderQuery->clone()->whereNotNull('coupon_code')->count();
        $couponOrderRate = $totalOrders > 0 ? ($couponOrders / $totalOrders) * 100 : 0;
        $totalRevenue = $orderQuery->sum('total_price');
        $affectedRevenueQuery = Order::where('status', '!=', 'Đã hủy');
        if ($from) $affectedRevenueQuery->whereDate('created_at', '>=', $from);
        if ($to) $affectedRevenueQuery->whereDate('created_at', '<=', $to);
        $affectedRevenue = $affectedRevenueQuery->sum('total_price');
        $originalRevenueQuery = Order::where('status', '!=', 'Đã hủy');
        if ($from) $originalRevenueQuery->whereDate('created_at', '>=', $from);
        if ($to) $originalRevenueQuery->whereDate('created_at', '<=', $to);
        $originalRevenue = $originalRevenueQuery->sum(DB::raw('total_price + discount_amount'));

        $pieChartData = [
            'shipping_usage' => $shippingUsages,
            'order_usage' => $orderUsages,
        ];
        $columnChartData = $this->getCouponUsageByTime($request);

        $categories = Category::with('children')->whereNull('parent_id')->get();


        return view('Admin.statistics.profit', compact(
            'productProfits',
            'orderProfits',
            'from',
            'to',
            'categories',
            'totalCoupons', 'activeCoupons', 'expiredCoupons', 'shippingCoupons', 'orderCoupons',
            'totalUsages', 'shippingUsages', 'orderUsages', 'totalDiscount', 'uniqueUsers',
            'topUsedCoupons', 'leastUsedCoupons', 'totalOrders', 'couponOrders', 'couponOrderRate', 'totalRevenue',
            'affectedRevenue', 'originalRevenue', 'pieChartData', 'columnChartData'
        ));
    }
    private function getCouponUsageByTime(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');

        $usageQuery = CouponUsage::with('coupon');
        if ($from) $usageQuery->whereDate('used_at', '>=', $from);
        if ($to) $usageQuery->whereDate('used_at', '<=', $to);

        $usages = $usageQuery->get();
        $timeData = [];
        $start = $from ? Carbon::parse($from) : null;
        $end = $to ? Carbon::parse($to) : null;
        $groupBy = ($start && $end && $end->diffInDays($start) <= 31) ? 'day' : 'month';

        foreach ($usages as $usage) {
            $key = $groupBy === 'day' ? $usage->used_at->format('Y-m-d') : $usage->used_at->format('Y-m');
            if (!isset($timeData[$key])) {
                $timeData[$key] = ['shipping' => 0, 'order' => 0];
            }
            $target = $usage->coupon->discount_target;
            $timeData[$key][$target == 'shipping_fee' ? 'shipping' : 'order']++;
        }

        ksort($timeData);
        return ['data' => $timeData, 'group_by' => $groupBy];
    }
    public function monthlyProfitChart(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $productName = $request->input('product_name');
        $categoryId = $request->input('category_id');
        $orderId = $request->input('order_id');

        $ordersQuery = Order::where('status', 'Hoàn thành')->with('orderItems');

        if ($from) {
            $ordersQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('created_at', '<=', $to);
        }
        if ($orderId) {
            $ordersQuery->where('id', $orderId);
        }

        $orders = $ordersQuery->get();
        $monthlyProfits = [];

        foreach ($orders as $order) {
            $orderRevenue = 0;
            $orderCost = 0;

            foreach ($order->orderItems as $item) {
                $variant = ProductVariant::find($item->variant_id);
                $product = Product::find($item->product_id);
                if (!$variant || !$product) continue;

                if ($productName && stripos($product->name, $productName) === false) {
                    continue;
                }

                if ($categoryId) {
                    $categoryIds = Category::getDescendantsAndSelfIds($categoryId);
                    if (!in_array($product->category_id, $categoryIds)) {
                        continue;
                    }
                }

                $itemRevenue = $item->price * $item->quantity;
                $itemCost = $variant->original_price * $item->quantity;

                $orderRevenue += $itemRevenue;
                $orderCost += $itemCost;
            }

            $profit = $orderRevenue - $orderCost;
            $monthKey = $order->created_at->format('Y-m');

            if (!isset($monthlyProfits[$monthKey])) {
                $monthlyProfits[$monthKey] = [
                    'month' => $monthKey,
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                ];
            }

            $monthlyProfits[$monthKey]['revenue'] += $orderRevenue;
            $monthlyProfits[$monthKey]['cost'] += $orderCost;
            $monthlyProfits[$monthKey]['profit'] += $profit;
        }

        ksort($monthlyProfits);

        return response()->json(array_values($monthlyProfits));
    }
}
