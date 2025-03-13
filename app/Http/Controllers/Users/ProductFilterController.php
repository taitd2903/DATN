<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductFilterController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')->with('children')->get(); // Lấy danh mục cha & con
        $query = Product::query();

        // Lọc theo danh mục
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo giới tính (nếu có)
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        $products = $query->with(['variants' => function ($query) {
            $query->select('product_id', 'price');
        }])->paginate(9); // Phân trang

        // Tính giá cao nhất và thấp nhất từ ProductVariant
        $products->each(function ($product) {
            $prices = $product->variants->pluck('price');
            $product->min_price = $prices->min();
            $product->max_price = $prices->max();
        });

        return view('users.categories.index', compact('categories', 'products'));
    }
}
