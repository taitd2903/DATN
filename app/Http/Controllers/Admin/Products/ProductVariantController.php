<?php
namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function destroy($productId, $variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);
        $variant->delete();

        return redirect()->back()->with('success', 'Biến thể đã được xóa thành công.');
    }
}
