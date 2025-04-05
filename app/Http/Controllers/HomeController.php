<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        $categories = Category::all(); 
        $banners = Banner::all();
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
        
    
        return view('users.products.index', compact('products', 'categories', 'banners', 'topSellingProductsByCategory', 'representativeProductsByParentCategory'));
    }
}
