<?php
namespace App\Http\Controllers\Users;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    
    // Hiển thị danh sách sản phẩm
    public function index(Request $request) {
        $categories = Category::all(); // Lấy danh sách danh mục
        $products = Product::with('category', 'variants');
        $banners = Banner::all();

        // Lọc theo tên sản phẩm
        if ($request->has('name') && $request->name != '') {
            $products->where('name', 'like', '%' . $request->name . '%');
        }

        // Lọc theo danh mục
        if ($request->has('category') && $request->category != '') {
            $products->where('category_id', $request->category);
        }

        // Lọc theo giới tính
        if ($request->has('gender') && $request->gender != '') {
            $products->where('gender', $request->gender);
        }

        $products = $products->get();

        // Tính tổng số lượng tồn kho, số lượng đã bán, giá thấp nhất và giá cao nhất
        foreach ($products as $product) {
            $product->total_stock_quantity = $product->variants->sum('stock_quantity');
            $product->total_sold_quantity = $product->variants->sum('sold_quantity');

            // Lấy giá thấp nhất và cao nhất từ các biến thể
            $prices = $product->variants->pluck('price');
            $product->min_price = $prices->min() ?? 0;
            $product->max_price = $prices->max() ?? 0;
        }

        return view('users.products.index', compact('products', 'categories', 'banners'));
    }

    // Hiển thị chi tiết sản phẩm
    public function show($id) {
        // $product = Product::with('category', 'variants')->findOrFail($id);
        
        // return view('users.products.show', compact('product'));
        $reviews = Review::where('product_id', $id)->latest()->get();
        $product = Product::with('category', 'variants')->findOrFail($id);
        $userHasPurchased = $this->hasPurchasedProduct($id); // Kiểm tra user đã mua hàng chưa

        return view('users.products.show', compact('product', 'userHasPurchased' ,'reviews'));
    }

    public function hasPurchasedProduct($productId)
{
    if (!auth()->check()) {
        return false; // Nếu chưa đăng nhập, không thể đánh giá
    }

    $userId = auth()->id();

    return DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.user_id', $userId)
        ->where('order_items.product_id', $productId)
        ->where('orders.status', 'Hoàn thành') // Chỉ kiểm tra đơn đã hoàn thành
        ->exists();
}

}
