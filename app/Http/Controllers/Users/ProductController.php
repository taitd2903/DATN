<?php
namespace App\Http\Controllers\Users;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller {
    
    // Hiển thị danh sách sản phẩm
    public function index() {
        $products = Product::with('category', 'variants')->get();
        
        // Tính tổng số lượng tồn kho và số lượng đã bán của tất cả biến thể
        foreach ($products as $product) {
            $product->total_stock_quantity = $product->variants->sum('stock_quantity');
            $product->total_sold_quantity = $product->variants->sum('sold_quantity');
        }
        
        return view('users.products.index', compact('products'));
    }

    // Hiển thị chi tiết sản phẩm
    public function show($id) {
        $product = Product::with('category', 'variants')->findOrFail($id);
        
        return view('users.products.show', compact('product'));
    }
}