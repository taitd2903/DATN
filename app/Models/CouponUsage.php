<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    // Bảng liên kết với model
    protected $table = 'coupon_usages';

    // Các cột có thể điền dữ liệu
    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_id',
        'used_at',
        'applied_discount',
    ];

    // Định dạng cột kiểu timestamp
    protected $casts = [
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'applied_discount' => 'float',
    ];

    // Quan hệ với model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với model Coupon
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // Quan hệ với model Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}