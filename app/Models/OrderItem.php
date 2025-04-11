<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable=
     [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'variant_id',
        'size',
        'color',
        'original_price',
        'is_available'
     ];
    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id' ,'id');
    }

    // Quan hệ với ProductVariant (nếu có)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'id');
    }
    


    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
