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
        
        $categories = Category::whereNull('parent_id')->with('children')->get();

       
        $query = Product::query()->where('is_delete', false);
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->filled('category')) {
            $selectedCategoryId = $request->category;

            $category = Category::with('children')->find($selectedCategoryId);

            if ($category) {
                if ($category->children->count() > 0) {
                  
                    $childIds = $category->children->pluck('id')->toArray();
                    $query->whereIn('category_id', $childIds);
                } else {
                   
                    $query->where('category_id', $selectedCategoryId);
                }
            }
        }

       
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

    
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

  
        $products = $query->with(['variants' => function ($q) {
            $q->select('product_id', 'price');
        }])->paginate(9)->appends($request->query());

       
        $products->each(function ($product) {
            $prices = $product->variants->pluck('price');
            $product->min_price = $prices->min();
            $product->max_price = $prices->max();
        });

        return view('users.categories.index', compact('categories', 'products'));
    }
}
?>