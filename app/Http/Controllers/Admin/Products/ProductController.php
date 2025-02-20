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

        // Tính tổng số lượng tồn kho của tất cả biến thể
        foreach ($products as $product) {
            $product->total_stock = $product->variants->sum('stock_quantity');
            $product->total_sold = $product->variants->sum('sold_quantity');
        }

        return view('Admin.products.index', compact('products'));
    }

    public function create() {
        $categories = Category::all();
        return view('Admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric',
            'gender' => 'required|in:male,female,unisex',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create($request->only(['name', 'image', 'description', 'base_price', 'category_id', 'gender']));

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variant['size'] ?? null,
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'stock_quantity' => $variant['stock_quantity'],
                    'sold_quantity' => $variant['sold_quantity'],
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
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric',
            'gender' => 'required|in:male,female,unisex',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
        ]);

        $product->update($request->only(['name', 'image', 'description', 'base_price', 'category_id', 'gender']));

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $variant['size'] ?? null, 'color' => $variant['color']],
                    [
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'sold_quantity' => $variant['sold_quantity'],
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
