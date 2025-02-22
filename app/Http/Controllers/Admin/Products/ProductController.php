<?php
namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function index() {
        $products = Product::with('category', 'variants')->get();
        $categories = Category::all();

        // Tính tổng số lượng tồn kho và tổng số lượng đã bán của tất cả biến thể
        foreach ($products as $product) {
            $product->total_stock = $product->variants->sum('stock_quantity');
            $product->total_sold = $product->variants->sum('sold_quantity');
        }

        return view('Admin.products.index', compact('products', 'categories'));
    }

    public function create() {
        $categories = Category::all();
        return view('Admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id', // Đổi thành nullable để không yêu cầu category_id
            'gender' => 'required|in:male,female,unisex',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.original_price' => 'required|numeric',
            'variants.*.price' => 'required|numeric', 
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
        ]);
    
        // Nếu không có category_id, giá trị mặc định là null
        $product = Product::create([
            'name' => $request->name,
            'image' => $request->image,
            'description' => $request->description,
            'category_id' => $request->category_id ?? null, // Kiểm tra nếu không có category_id thì gán null
            'gender' => $request->gender
        ]);
    
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variant['size'] ?? null,
                    'color' => $variant['color'],
                    'original_price' => $variant['original_price'],
                    'price' => $variant['price'],
                    'stock_quantity' => $variant['stock_quantity'],
                    'sold_quantity' => $variant['sold_quantity'],
                    'image' => $variant['image'] ?? null,
                ]);
            }
        }
    
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm!');
    }
    

    public function edit(Product $product) {
        $categories = Category::all();
        return view('Admin.products.edit', compact('product', 'categories'));
    }
    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id', // Để category_id có thể không có
            'gender' => 'required|in:male,female,unisex',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.original_price' => 'required|numeric',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
        ]);
    
        // Cập nhật sản phẩm, nếu không có category_id thì giữ nguyên giá trị cũ của nó
        $product->update([
            'name' => $request->name,
            'image' => $request->image,
            'description' => $request->description,
            'category_id' => $request->has('category_id') ? $request->category_id : $product->category_id, // Nếu có category_id thì cập nhật, nếu không thì giữ nguyên giá trị cũ
            'gender' => $request->gender,
        ]);
    
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $variant['size'] ?? null, 'color' => $variant['color']],
                    [
                        'original_price' => $variant['original_price'],
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'sold_quantity' => $variant['sold_quantity'],
                        'image' => $variant['image'] ?? null,
                    ]
                );
            }
        }
    
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật!');
    }
    
    

    public function destroy(Product $product) {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa!');
    }
}
