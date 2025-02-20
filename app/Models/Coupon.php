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
        'code', // Mã coupon
        'description', // Mô tả
        'discount_type', // Loại giảm giá (1: Phần trăm, 2: Giá trị cố định)
        'discount_value', // Giá trị giảm giá (có thể là % hoặc số tiền)
        'start_date', // Ngày bắt đầu
        'end_date', // Ngày kết thúc
        'usage_limit', // Tổng số lần có thể sử dụng
        'used_count', // Số lần đã sử dụng
        'usage_per_user', // Số lần sử dụng tối đa cho mỗi người
        'status', // Trạng thái (1: Hoạt động, 2: Dừng hoạt động)
        'max_discount_amount', // Số tiền giảm tối đa (nếu là %)
        'user_voucher_limit', // Loại người dùng được áp dụng (1: Tất cả, 2: Người cụ thể, 3: Giới tính)
        'title' // Tiêu đề coupon
    ];

    // Ép kiểu dữ liệu cho các cột để sử dụng thuận tiện hơn
    protected $casts = [
        'start_date' => 'date', // Chuyển đổi start_date thành Carbon để dễ sử dụng
        'end_date' => 'date', // Chuyển đổi end_date thành Carbon
    ];

    /**
     * Scope lọc ra các coupon đang hoạt động (chưa hết hạn)
     * Sử dụng: Coupon::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)
                     ->whereDate('end_date', '>=', Carbon::today());
    }

    /**
     * Kiểm tra xem coupon có hết hạn hay chưa
     * Sử dụng: $coupon->isExpired();
     */
    public function isExpired()
    {
        return Carbon::today()->greaterThan($this->end_date);
    }

    /**
     * Quan hệ nhiều-nhiều với bảng users để xác định người dùng cụ thể có thể sử dụng coupon
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    /**
     * Kiểm tra xem một user có thể sử dụng coupon hay không
     * @param User $user
     * @return bool
     */
    public function isValidForUser(User $user): bool
    {
        if ($this->user_voucher_limit == 1) {
            return true; // Áp dụng cho tất cả người dùng
        }

        if ($this->user_voucher_limit == 2) {
            return $this->users->contains($user->id); // Chỉ áp dụng cho user được chọn
        }

        if ($this->user_voucher_limit == 3) {
            // Kiểm tra nếu user có giới tính hợp lệ
            return in_array($user->gender, ['male', 'female']);
        }

        return false;
    }
}
