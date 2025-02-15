<?php
namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function index() {
        // Lấy tất cả các sản phẩm cùng với các biến thể của chúng
        $products = Product::with('category', 'variants')->get();

        // Tính tổng số sản phẩm của tất cả các biến thể cộng lại
        foreach ($products as $product) {
            $product->total_quantity = $product->variants->sum('quantity');
        }

        $totalProducts = $products->sum('total_quantity'); // Tổng số lượng của tất cả các sản phẩm và biến thể

        return view('Admin.products.index', compact('products', 'totalProducts'));
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
            'variants.*.size' => 'required|string|max:255',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric',
            'variants.*.quantity' => 'required|integer',
        ]);

        $product = Product::create($request->only(['name', 'image', 'description', 'base_price', 'category_id']));

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'quantity' => $variant['quantity'],
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được thêm!');
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
            'variants.*.size' => 'required|string|max:255',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric',
            'variants.*.quantity' => 'required|integer',
        ]);

        $product->update($request->only(['name', 'image', 'description', 'base_price', 'category_id']));

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $variant['size'], 'color' => $variant['color']],
                    ['price' => $variant['price'], 'quantity' => $variant['quantity']]
                );
            }
        }

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được cập nhật!');
    }

    public function destroy(Product $product) {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa!');
    }
}
