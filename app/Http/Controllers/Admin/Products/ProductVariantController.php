<?php
namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function create($productId)
    {
        $product = Product::findOrFail($productId);

       
        $hasSizeVariants = ProductVariant::where('product_id', $productId)
            ->whereNotNull('size')
            ->exists();

        return view('admin.variants.create', compact('product', 'hasSizeVariants'));
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'size' => 'nullable|string',
            'color' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'required|image|max:2048'
        ]);

        $product = Product::findOrFail($productId);

        // Kiểm tra trùng lặp
        $query = ProductVariant::where('product_id', $productId)
            ->where('color', $request->color);

        if ($request->size) {
            $query->where('size', $request->size);
        }

        $exists = $query->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Biến thể với màu và size này đã tồn tại!'])->withInput();
        }

        // Tạo biến thể mới
        $variant = new ProductVariant();
        $variant->product_id = $productId;
        $variant->size = $request->size;
        $variant->color = $request->color;
        $variant->original_price = $request->original_price;
        $variant->price = $request->price;
        $variant->stock_quantity = $request->stock_quantity;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
            $variant->image = $imagePath;
        }

        $variant->save();

        return redirect()->route('admin.products.edit', $productId)->with('success', 'Biến thể đã được thêm thành công!');
    }
    public function destroy($productId, $variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);
        $variant->delete();

        return redirect()->back()->with('success', 'Biến thể đã được xóa thành công.');
    }
}
    
    
