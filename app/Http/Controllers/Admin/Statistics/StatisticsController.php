<?php

namespace App\Http\Controllers\Admin\Statistics;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        $categories = Category::with('children')->whereNull('parent_id')->get();


        return view('Admin.statistics.profit', compact(
            'productProfits',
            'orderProfits',
            'from',
            'to',
            'categories'
        ));
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
