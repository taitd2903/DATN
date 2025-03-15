<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Events\PriceUpdated;

class ProductVariantObserver
{
    /**
     * Xử lý khi ProductVariant được cập nhật
     */
    public function updated(ProductVariant $variant)
    {
        // Kiểm tra xem trường 'price' có thay đổi không
        if ($variant->isDirty('price')) {
            $newPrice = $variant->price;

            // Cập nhật giá trong tất cả CartItem liên quan
            $cartItems = CartItem::where('variant_id', $variant->id)->get();
            foreach ($cartItems as $cartItem) {
                $cartItem->price = $newPrice;
                $cartItem->save();
                event(new PriceUpdated($cartItem));
            }
        }
    }
}