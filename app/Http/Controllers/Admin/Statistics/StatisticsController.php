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
use App\Models\OrderReturn;
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
        $gender = $request->input('gender');

        $ordersQuery = Order::where('status', 'Hoàn thành')->with('orderItems');

        if ($from) {
            $ordersQuery->whereDate('complete_ship', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('complete_ship', '<=', $to);
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
                if ($gender && $product->gender !== $gender) {
                    continue;
                }
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
                $itemCost = $item->original_price * $item->quantity;
                $itemProfit = $itemRevenue - $itemCost;
                $variantKey = $item->size;

                if (!isset($productProfits[$product->id])) {
                    $productProfits[$product->id] = [
                        'product_name' => $product->name,
                        'total_revenue' => 0,
                        'total_cost' => 0,
                        'total_profit' => 0,
                        'total_sold' => 0,
                        'size' => $item->size,
                        'color' => $item->color,
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
        $totalDiscountQuery = Order::whereNotNull('coupon_code')->where('status', 'Hoàn thành');
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
        $affectedRevenueQuery = Order::where('status', 'Hoàn thành');
        if ($from) $affectedRevenueQuery->whereDate('created_at', '>=', $from);
        if ($to) $affectedRevenueQuery->whereDate('created_at', '<=', $to);
        $affectedRevenue = $affectedRevenueQuery->sum('total_price');
        $originalRevenueQuery = Order::where('status', 'Hoàn thành');
        if ($from) $originalRevenueQuery->whereDate('created_at', '>=', $from);
        if ($to) $originalRevenueQuery->whereDate('created_at', '<=', $to);
        $originalRevenue = $originalRevenueQuery->sum(DB::raw('total_price + discount_amount'));

        $pieChartData = [
            'shipping_usage' => $shippingUsages,
            'order_usage' => $orderUsages,
        ];
        $columnChartData = $this->getCouponUsageByTime($request);

        $categories = Category::with('children')->whereNull('parent_id')->get();

// Thống kê tỷ lệ hủy đơn
$allOrdersQuery = Order::query()->with('orderItems.product');
$cancelledOrdersQuery = Order::where('status', 'Hủy')->with('orderItems.product');

$productName = $request->input('product_name');
$categoryId = $request->input('category_id');
$gender = $request->input('gender');

if ($from) {
    $allOrdersQuery->whereDate('created_at', '>=', $from);
    $cancelledOrdersQuery->whereDate('created_at', '>=', $from);
}
if ($to) {
    $allOrdersQuery->whereDate('created_at', '<=', $to);
    $cancelledOrdersQuery->whereDate('created_at', '<=', $to);
}

// Lọc theo tên sản phẩm, giới tính, danh mục
$applyProductFilter = function ($query) use ($productName, $gender, $categoryId) {
    $query->whereHas('product', function ($productQuery) use ($productName, $gender, $categoryId) {
        if ($productName) {
            $productQuery->where('name', 'like', '%' . $productName . '%');
        }

        if ($gender) {
            $productQuery->where('gender', $gender);
        }

        if ($categoryId) {
            $categoryIds = \App\Models\Category::getDescendantsAndSelfIds($categoryId);
            $productQuery->whereIn('category_id', $categoryIds);
        }
    });
};

$allOrdersQuery->whereHas('orderItems', $applyProductFilter);
$cancelledOrdersQuery->whereHas('orderItems', $applyProductFilter);

$totalAllOrders = $allOrdersQuery->count();
$totalCancelledOrders = $cancelledOrdersQuery->count();
$cancelledOrderRate = $totalAllOrders > 0 ? ($totalCancelledOrders / $totalAllOrders) * 100 : 0;


// Thống kê hoàn hàng
$returnStats = $this->returnStatistics($request);
$returnChartData = $this->getReturnStatisticsByTime($request);
        return view('Admin.statistics.profit', compact(
            'productProfits',
            'orderProfits',
            'from',
            'to',
            'categories',
            'totalCoupons', 'activeCoupons', 'expiredCoupons', 'shippingCoupons', 'orderCoupons',
            'totalUsages', 'shippingUsages', 'orderUsages', 'totalDiscount', 'uniqueUsers',
            'topUsedCoupons', 'leastUsedCoupons', 'totalOrders', 'couponOrders', 'couponOrderRate', 'totalRevenue',
            'affectedRevenue', 'originalRevenue', 'pieChartData', 'columnChartData',
            'totalAllOrders', 'totalCancelledOrders', 'cancelledOrderRate','returnStats', 'returnChartData'
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
        $gender = $request->input('gender');
    
        $ordersQuery = Order::where('status', 'Hoàn thành')
            ->whereNotNull('complete_ship')
            ->with('orderItems');
    
        if ($from) {
            $ordersQuery->whereDate('complete_ship', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('complete_ship', '<=', $to);
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
    
                if ($gender && $product->gender !== $gender) {
                    continue;
                }
    
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
                $itemCost = $item->original_price * $item->quantity;
    
                $orderRevenue += $itemRevenue;
                $orderCost += $itemCost;
            }
    
            $profit = $orderRevenue - $orderCost;
            $monthKey = \Carbon\Carbon::parse($order->complete_ship)->format('Y-m');
    
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
    




    public function returnStatistics(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('to_date');
    $productName = $request->input('product_name');
    $categoryId = $request->input('category_id');
    $gender = $request->input('gender');

    $allOrdersQuery = Order::query()->with('orderItems.product');
    $returnOrdersQuery = OrderReturn::query()->with('order.orderItems.product');

    if ($from) {
        $returnOrdersQuery->whereDate('order_returns.created_at', '>=', $from);
    }
    if ($to) {
        $returnOrdersQuery->whereDate('order_returns.created_at', '<=', $to);
    }
    

    $applyProductFilter = function ($query) use ($productName, $gender, $categoryId) {
        $query->whereHas('product', function ($productQuery) use ($productName, $gender, $categoryId) {
            if ($productName) {
                $productQuery->where('name', 'like', '%' . $productName . '%');
            }
            if ($gender) {
                $productQuery->where('gender', $gender);
            }
            if ($categoryId) {
                $categoryIds = Category::getDescendantsAndSelfIds($categoryId);
                $productQuery->whereIn('category_id', $categoryIds);
            }
        });
    };

    $allOrdersQuery->whereHas('orderItems', $applyProductFilter);
    $returnOrdersQuery->whereHas('order.orderItems', $applyProductFilter);

    $totalAllOrders = $allOrdersQuery->count();
    $totalReturnOrders = $returnOrdersQuery->count();
    $returnOrderRate = $totalAllOrders > 0 ? ($totalReturnOrders / $totalAllOrders) * 100 : 0;

    $returnByStatus = $returnOrdersQuery->select('order_returns.status', DB::raw('count(*) as count'))
        ->groupBy('order_returns.status')
        ->pluck('count', 'order_returns.status')
        ->toArray();

        $refundedQuery = clone $returnOrdersQuery;

        $totalRefundedAmount = $refundedQuery
            ->join('orders', 'order_returns.order_id', '=', 'orders.id')
            ->where('order_returns.status', 'approved')
            ->sum('orders.total_price');
        
    return [
        'total_return_orders' => $totalReturnOrders,
        'return_order_rate' => $returnOrderRate,
        'return_by_status' => $returnByStatus,
        'total_all_orders' => $totalAllOrders,
        'total_refunded_amount' => $totalRefundedAmount,
    ];
}

private function getReturnStatisticsByTime(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('to_date');
    $productName = $request->input('product_name');
    $categoryId = $request->input('category_id');
    $gender = $request->input('gender');

    $returnQuery = OrderReturn::with('order.orderItems.product');
    if ($from) $returnQuery->whereDate('created_at', '>=', $from);
    if ($to) $returnQuery->whereDate('created_at', '<=', $to);

    // Apply product filters
    $applyProductFilter = function ($query) use ($productName, $gender, $categoryId) {
        $query->whereHas('product', function ($productQuery) use ($productName, $gender, $categoryId) {
            if ($productName) {
                $productQuery->where('name', 'like', '%' . $productName . '%');
            }
            if ($gender) {
                $productQuery->where('gender', $gender);
            }
            if ($categoryId) {
                $categoryIds = Category::getDescendantsAndSelfIds($categoryId);
                $productQuery->whereIn('category_id', $categoryIds);
            }
        });
    };

    $returnQuery->whereHas('order.orderItems', $applyProductFilter);

    $returns = $returnQuery->get();
    $timeData = [];
    $start = $from ? Carbon::parse($from) : null;
    $end = $to ? Carbon::parse($to) : null;
    $groupBy = ($start && $end && $end->diffInDays($start) <= 31) ? 'day' : 'month';

    foreach ($returns as $return) {
        $key = $groupBy === 'day' ? $return->created_at->format('Y-m-d') : $return->created_at->format('Y-m');
        if (!isset($timeData[$key])) {
            $timeData[$key] = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        }
        $timeData[$key][$return->status]++;
    }

    ksort($timeData);
    return ['data' => $timeData, 'group_by' => $groupBy];
}
}
