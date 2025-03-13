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
    
        // Lọc theo giá
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }
    
        // Lọc theo giới tính (nếu có)
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
    
        $products = $query->paginate(9); // Phân trang
    
        return view('users.categories.index', compact('categories', 'products'));
    }
    
}
