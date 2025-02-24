<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
    ];

    // Liên kết với bảng Users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Liên kết với bảng Products
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Liên kết với bảng ProductVariants
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
