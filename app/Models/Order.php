<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
        'payment_status',
        'note',
        'vnp_txn_ref',
        'coupon_code',
        'status_updated_at',
        'status_updated_by',
        'delivering_at',
        'completed_at'

    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }
}
