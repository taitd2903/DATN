<?php
namespace App\Http\Controllers\Users;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use App\Models\Article;


class ProductController extends Controller {

    public function index(Request $request) {
        // $breadcrumbs = [
        //     ['name' => 'Trang chủ', 'url' => route('home')],
        // ];
        $categories = Category::all();
        $banners = Banner::all();
        $articles = Article::where('is_active', true)->latest()->take(3)->get();
        $products = Product::with('category', 'variants') ->where('is_delete', false);
        if ($request->has('name') && $request->name != '') {
            $products->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('category') && $request->category != '') {
            $products->where('category_id', $request->category);
        }
        if ($request->has('gender') && $request->gender != '') {
            $products->where('gender', $request->gender);
        }

        $products = $products->get();

        // Tính toán số lượng tồn kho, đã bán, giá min/max cho từng sản phẩm
        foreach ($products as $product) {
            $product->total_stock_quantity = $product->variants->sum('stock_quantity');
            $product->total_sold_quantity = $product->variants->sum('sold_quantity');

            // Lấy giá thấp nhất và cao nhất từ các biến thể
            $prices = $product->variants->pluck('price');
            $product->min_price = $prices->min() ?? 0;
            $product->max_price = $prices->max() ?? 0;
        }

        $topSellingProductsByCategory = [];
        foreach ($categories as $category) {

            $topSellingProductsByCategory[$category->id] = Product::where('category_id', $category->id)
                ->with('variants')
                ->get()
                ->sortByDesc(function ($product) {
                    return $product->variants->sum('sold_quantity');
                })
                ->take(10);
        }

        $representativeProductsByParentCategory = [];
        $parentCategories = Category::whereNull('parent_id')->get();

        foreach ($parentCategories as $parentCategory) {
            $childCategories = Category::where('parent_id', $parentCategory->id)->pluck('id');
            $topProducts = Product::whereIn('category_id', $childCategories)
                ->with('variants')
                ->get()
                ->sortByDesc(function ($product) {
                    return $product->variants->sum('sold_quantity');
                })
                ->take(10);

            $representativeProduct = $topProducts->first();

            if ($representativeProduct) {
                $prices = $representativeProduct->variants->pluck('price');
                $representativeProduct->min_price = $prices->min() ?? 0;
                $representativeProduct->max_price = $prices->max() ?? 0;
                $representativeProduct->avg_price = $prices->avg() ?? 0; // Thêm giá trung bình
            }

            $representativeProductsByParentCategory[$parentCategory->id] = $representativeProduct;
        }

        $topSellingProducts = Product::with('category', 'variants')
        ->where('is_delete', false)
        ->get()
        ->sortByDesc(function ($product) {
            return $product->variants->sum('sold_quantity');
        })
        ->take(4); // Lấy 5 sản phẩm bán chạy nhất

    // Tính toán số lượng tồn kho, đã bán, giá min/max cho sản phẩm bán chạy
    foreach ($topSellingProducts as $product) {
        $product->total_stock_quantity = $product->variants->sum('stock_quantity');
        $product->total_sold_quantity = $product->variants->sum('sold_quantity');
        $prices = $product->variants->pluck('price');
        $product->min_price = $prices->min() ?? 0;
        $product->max_price = $prices->max() ?? 0;
    }


        return view('users.products.index', compact( 'articles','products', 'categories', 'banners', 'topSellingProductsByCategory', 'representativeProductsByParentCategory','topSellingProducts'));
    }




    public function show($id)
    {
        $product = Product::with('category', 'variants')->findOrFail($id);
        $reviews = Review::where('product_id', $id)->latest()->get();
        $userHasPurchased = auth()->check() ? $this->hasPurchasedProduct($id) : false;
        $minPrice = $product->variants->min('price');
        $maxPrice = $product->variants->max('price');
        // Lấy đơn hàng gần nhất của user (nếu có)
        $order = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', auth()->id())
            ->where('order_items.product_id', $id)
            ->where('orders.status', 'Hoàn thành')
            ->orderBy('orders.created_at', 'desc')
            ->select('orders.id')
            ->first();

        // Kiểm tra nếu user đã đánh giá đơn hàng gần nhất
        $userCanReview = false;
        if ($order) {
            $userCanReview = !Review::where('order_id', $order->id)
                ->where('user_id', auth()->id())
                ->where('product_id', $id)
                ->exists();
        }
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Chi tiết sản phẩm', 'url' => null],
            ['name' => $product->name, 'url' => null],
        ];
        $topSellingProducts = Product::with('category', 'variants')
            ->where('is_delete', false)
            ->get()
            ->sortByDesc(function ($product) {
                return $product->variants->sum('sold_quantity');
            })
            ->take(4); // Lấy 5 sản phẩm bán chạy nhất

        // Tính toán số lượng tồn kho, đã bán, giá min/max cho sản phẩm bán chạy
        foreach ($topSellingProducts as $product) {
            $product->total_stock_quantity = $product->variants->sum('stock_quantity');
            $product->total_sold_quantity = $product->variants->sum('sold_quantity');
            $prices = $product->variants->pluck('price');
            $product->min_price = $prices->min() ?? 0;
            $product->max_price = $prices->max() ?? 0;
        }

        

        return view('users.products.show', compact('breadcrumbs','product', 'reviews', 'userCanReview','minPrice' ,'maxPrice','topSellingProducts'));
    }


//     public function hasPurchasedProduct($productId)
// {
//     if (!auth()->check()) {
//         return false; // Nếu chưa đăng nhập, không thể đánh giá
//     }

//     $userId = auth()->id();

//     return DB::table('orders')
//         ->join('order_items', 'orders.id', '=', 'order_items.order_id')
//         ->where('orders.user_id', $userId)
//         ->where('order_items.product_id', $productId)
//         ->where('orders.status', 'Hoàn thành') // Chỉ kiểm tra đơn đã hoàn thành
//         ->exists();
// }




public function hasPurchasedProduct($productId)
{
    if (!auth()->check()) {
        return false;
    }

    $userId = auth()->id();

    // Lấy danh sách order_id của user đã mua sản phẩm này
    $orderIds = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.user_id', $userId)
        ->where('order_items.product_id', $productId)
        ->where('orders.status', 'Hoàn thành')
        ->pluck('orders.id');

    if ($orderIds->isEmpty()) {
        return false;
    }

    // Kiểm tra xem user đã đánh giá tất cả các đơn này chưa
    $hasUnreviewedOrders = DB::table('order_items')
        ->whereIn('order_id', $orderIds)
        ->where('product_id', $productId)
        ->whereNotExists(function ($query) use ($userId, $productId) {
            $query->select(DB::raw(1))
                ->from('reviews')
                ->whereRaw('reviews.order_id = order_items.order_id')
                ->where('reviews.user_id', $userId)
                ->where('reviews.product_id', $productId);
        })
        ->exists();

    return $hasUnreviewedOrders;
}

}
