<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description', 
        'discount_type', 
        'discount_value', 
        'start_date', 
        'end_date', 
        'usage_limit', 
        'used_count', 
        'usage_per_user', 
        'status', 
        'max_discount_amount', 
        'user_voucher_limit', 
        'title', 
        'gender',
        'minimum_order_value'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date', 
    ];

    /**
     * 
     * 
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)
                     ->whereDate('end_date', '>=', Carbon::today());
    }

    /**
     * 
     * 
     */
    public function isExpired()
    {
        return Carbon::today()->greaterThan($this->end_date);
    }

    /**
     * 
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user', 'coupon_id', 'user_id')
                    ->withTimestamps();
    }
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    /**
     * Kiểm tra xem một user có thể sử dụng coupon hay không
     * @param User $user
     * @return bool
     */
    // public function isValidForUser(User $user): bool
    // {
    //     if ($this->user_voucher_limit == 1) {
    //         return true; // Áp dụng cho tất cả người dùng
    //     }

    //     if ($this->user_voucher_limit == 2) {
    //         return $this->users->contains($user->id); // Chỉ áp dụng cho user được chọn
    //     }

    //     if ($this->user_voucher_limit == 3) {
    //         // Kiểm tra nếu user có giới tính hợp lệ
    //         return in_array($user->gender, ['male', 'female']);
    //     }

    //     return false;
    // }
}
