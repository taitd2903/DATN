<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller {

    public function index() {
        $products = Product::with('category', 'variants') ->where('is_delete', false)->get();
        $categories = Category::all();

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'gender' => 'required|in:male,female,unisex',
            'variants.*.size' => 'nullable|string|max:255',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.original_price' => 'required|numeric',
            'variants.*.price' => 'required|numeric', 
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload ảnh sản phẩm nếu có
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/products', 'public');
        }

        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'image' => $imagePath, 
            'description' => $request->description,
            'long_description' => $request->long_description,
            'category_id' => $request->category_id ?? null,
            'gender' => $request->gender
        ]);

        // Thêm biến thể a
        if ($request->has('variants')) {
            foreach ($request->variants as $key => $variant) {
                // Nếu biến thể có ID → Cập nhật
                if (!empty($variant['id'])) {
                    $existingVariant = ProductVariant::find($variant['id']);
        
                    if ($existingVariant) {
                        $variantImagePath = $existingVariant->image;
        
                        // Nếu có ảnh mới, lưu và xóa ảnh cũ
                        if ($request->hasFile("variants.$key.image")) {
                            if ($existingVariant->image) {
                                Storage::disk('public')->delete($existingVariant->image);
                            }
                            $variantImagePath = $request->file("variants.$key.image")->store('uploads/variants', 'public');
                        }
        
                      
                        $existingVariant->update([
                            'size' => $variant['size'] ?? null,
                            'color' => $variant['color'],
                            'original_price' => $variant['original_price'],
                            'price' => $variant['price'],
                            'stock_quantity' => $variant['stock_quantity'],
                            'sold_quantity' => $variant['sold_quantity'],
                            'image' => $variantImagePath,
                        ]);
                    }
                } else {
                    // Nếu không có ID → Thêm mới biến thể
                    $variantImagePath = null;
        
                    // Nếu có ảnh, lưu ảnh mới
                    if ($request->hasFile("variants.$key.image")) {
                        $variantImagePath = $request->file("variants.$key.image")->store('uploads/variants', 'public');
                    }
        
                   
        
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'],
                        'original_price' => $variant['original_price'],
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'sold_quantity' => $variant['sold_quantity'],
                        'image' => $variantImagePath,
                    ]);
                }
            }
        }
        

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm!');
    }

    public function edit(Product $product) {
        $categories = Category::orderBy('parent_id')->orderBy('name')->get();
        return view('Admin.products.edit', compact('product', 'categories'));
    }
    

    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required|string|max:255',
            'variants.*.size' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'gender' => 'required|in:male,female,unisex',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.original_price' => 'required|numeric',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.sold_quantity' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Nếu có ảnh mới, xóa ảnh cũ và cập nhật ảnh mới
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('uploads/products', 'public');
        }

        // Cập nhật sản phẩm
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'long_description' => $request->long_description,
            'category_id' => $request->has('category_id') ? $request->category_id : $product->category_id,
            'gender' => $request->gender,
        ]);

        if ($request->has('variants')) {
            foreach ($request->variants as $key => $variant) {
                if (empty($variant['id'])) {
                    continue;
                }
        
                $existingVariant = ProductVariant::find($variant['id']);
                if ($existingVariant) {
                    $variantImagePath = $existingVariant->image; 
                    if ($request->hasFile("variants.$key.image")) {
                        if ($existingVariant->image) {
                            Storage::disk('public')->delete($existingVariant->image);
                        }
                        $variantImagePath = $request->file("variants.$key.image")->store('uploads/variants', 'public');
                    }
                    $existingVariant->update([
                       'size' => $variant['size'] ?? null,
                        'color' => $variant['color'],
                        'original_price' => $variant['original_price'],
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'sold_quantity' => $variant['sold_quantity'],
                        'image' => $variantImagePath,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật!');
    }
// xóa cứng
    // public function destroy(Product $product) {
        
    //     if ($product->image) {
    //         Storage::disk('public')->delete($product->image);
    //     }

    //     // Xóa ảnh biến thể
    //     foreach ($product->variants as $variant) {
    //         if ($variant->image) {
    //             Storage::disk('public')->delete($variant->image);
    //         }
    //     }

    //     $product->delete();
    //     return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa!');
    // }

// xóa mềm
public function destroy(Product $product) {
    $product->is_delete = true;
    $product->save();

    return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cho vào thùng rác!');
}




    public function show($id) {
        $product = Product::with('variants', 'category')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }
}
