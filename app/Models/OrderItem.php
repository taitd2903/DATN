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
        'is_available'
     ];
     public function product()
{
    return $this->belongsTo(Product::class);
}

public function variant()
{
    return $this->belongsTo(ProductVariant::class, 'variant_id');
}

}
