<?php
namespace App\Http\Controllers\Users;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

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
        $product = Product::with('category', 'variants')->findOrFail($id);
        
        return view('users.products.show', compact('product'));
    }
}
