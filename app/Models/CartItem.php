<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'variant_id', 'quantity', 'price'];
    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function variant() {
        return $this->belongsTo(ProductVariant::class);
    }
}
