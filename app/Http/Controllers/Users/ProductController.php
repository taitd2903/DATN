<?php
namespace App\Http\Controllers\Users;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index() {
        // Lấy tất cả sản phẩm và các biến thể
        $products = Product::with('category', 'variants')->get();

        // Tính tổng số lượng của từng sản phẩm
        foreach ($products as $product) {
            $product->total_quantity = $product->variants->sum('quantity');
        }

        return view('Users.products.index', compact('products'));
    }

    public function show($id) {
        
        $product = Product::with('variants')->findOrFail($id);

        return view('Users.products.show', compact('product'));
    }
}
