<?php

namespace App\Http\Controllers\Admin\Coupons;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        Coupon::where('status', 1)
        ->where('end_date', '<', Carbon::today())
        ->update(['status' => 2]);

        $query = Coupon::query();
        $query->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
            return $q->where('status', $request->status);
        });
        $coupons = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('Admin.Coupons.index', compact('coupons'));
    }

    public function create()
    {
        // Lấy danh sách người dùng để hiển thị nếu chọn "Người dùng cụ thể"
        $users = User::select('id', 'name', 'email')->get();
        return view('Admin.Coupons.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|integer|in:1,2',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'required|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
            'status' => 'required|integer|in:1,2',
            'max_discount_amount' => 'nullable|integer|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'user_voucher_limit' => 'required|integer|in:1,2,3', // 1: Tất cả, 2: Người cụ thể, 3: Giới tính
            'selected_users' => 'nullable|array',  // Danh sách ID người dùng (chỉ khi chọn "Người dùng cụ thể")
            'title' => 'nullable|string|max:255'
        ], [
            'code.required' => 'Mã giảm giá không được để trống.',
            'code.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'discount_type.required' => 'Vui lòng nhập loại giảm giá.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm không được để trống.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm không thể nhỏ hơn 0.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải từ hôm nay trở đi.',
            'end_date.required' => 'Ngày kết thúc không được để trống.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'usage_limit.required' => 'Số lượng không được để trống.',
            'usage_limit.integer' => 'Số lượng phải là số nguyên.',
            'usage_limit.min' => 'Số lượng ít nhất là 1.',
            'usage_per_user.required' => 'Số lần sử dụng mỗi người không được để trống.',
            'usage_per_user.integer' => 'Số lần sử dụng phải là số nguyên.',
            'usage_per_user.min' => 'Số lần sử dụng phải ít nhất là 1.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'max_discount_amount.integer' => 'Mức giảm tối đa phải là số nguyên.',
            'max_discount_amount.min' => 'Mức giảm tối đa không thể nhỏ hơn 0.',
            'user_voucher_limit.required' => 'Giới hạn người dùng không được để trống.',
            'user_voucher_limit.in' => 'Giới hạn người dùng không hợp lệ.',
            'selected_users.array' => 'Danh sách người dùng không hợp lệ.',
        ], [
            'minimum_order_value.numeric' => 'Giá trị tối thiểu đơn hàng phải là số.',
            'minimum_order_value.min' => 'Giá trị tối thiểu đơn hàng không thể nhỏ hơn 0.',
        ]);

        // Tạo coupon mới
        $coupon = Coupon::create($request->except('selected_users'));

        // Nếu chọn "Người dùng cụ thể", gán danh sách người dùng vào bảng trung gian coupon_user
        if ($request->user_voucher_limit == 2 && $request->has('selected_users')) {
            $coupon->users()->sync($request->selected_users);
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã tạo thành công!');
    }

    public function edit(Coupon $coupon)
    {
        // Lấy danh sách người dùng cho mục chọn "Người dùng cụ thể"
        $users = User::select('id', 'name', 'email')->get();
        return view('Admin.Coupons.edit', compact('coupon', 'users'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|integer|in:1,2',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required','date',
            function ($attribute, $value, $fail) use ($coupon) {
                if ($value != $coupon->start_date && $value < now()->toDateString()) {
                    $fail('Ngày bắt đầu phải từ hôm nay trở đi khi thay đổi.');
                }
            },
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'required|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
            'status' => 'required|integer|in:1,2',
            'max_discount_amount' => 'nullable|integer|min:0',
            'user_voucher_limit' => 'required|integer|in:1,2,3', // 1: Tất cả, 2: Người cụ thể, 3: Giới tính
            'selected_users' => 'nullable|array',  // Danh sách ID người dùng (chỉ khi chọn "Người dùng cụ thể")
            'title' => 'nullable|string|max:255'
        ], [
            'code.required' => 'Mã giảm giá không được để trống.',
            'code.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'discount_type.required' => 'Vui lòng nhập loại giảm giá.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm không được để trống.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm không thể nhỏ hơn 0.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải từ hôm nay trở đi.',
            'end_date.required' => 'Ngày kết thúc không được để trống.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'usage_limit.required' => 'Số lượng không được để trống.',
            'usage_limit.integer' => 'Số lượng phải là số nguyên.',
            'usage_limit.min' => 'Số lượng ít nhất là 1.',
            'usage_per_user.required' => 'Số lần sử dụng mỗi người không được để trống.',
            'usage_per_user.integer' => 'Số lần sử dụng phải là số nguyên.',
            'usage_per_user.min' => 'Số lần sử dụng phải ít nhất là 1.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'max_discount_amount.integer' => 'Mức giảm tối đa phải là số nguyên.',
            'max_discount_amount.min' => 'Mức giảm tối đa không thể nhỏ hơn 0.',
            'user_voucher_limit.required' => 'Giới hạn người dùng không được để trống.',
            'user_voucher_limit.in' => 'Giới hạn người dùng không hợp lệ.',
            'selected_users.array' => 'Danh sách người dùng không hợp lệ.',
        ], [
            'minimum_order_value.numeric' => 'Giá trị tối thiểu đơn hàng phải là số.',
            'minimum_order_value.min' => 'Giá trị tối thiểu đơn hàng không thể nhỏ hơn 0.',
        ]);

        // Cập nhật dữ liệu
        $coupon->update($request->except('selected_users'));

        // Nếu loại giới hạn người dùng thay đổi, cần cập nhật lại danh sách
        if ($request->user_voucher_limit == 2) { // Chọn "Người dùng cụ thể"
            if ($request->has('selected_users')) {
                $coupon->users()->sync($request->selected_users); // Cập nhật danh sách người dùng
            }
        } else {
            // Nếu chuyển sang "Tất cả" hoặc "Giới tính", xóa danh sách người dùng cũ
            $coupon->users()->detach();
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã cập nhật thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        // Xóa danh sách người dùng trước khi xóa coupon
        $coupon->users()->detach();
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã xoá thành công!');
    }

    /**
     * Kiểm tra xem người dùng có đủ điều kiện sử dụng coupon không
     */
    public function isEligibleForCoupon(User $user, Coupon $coupon)
    {
        if ($coupon->user_voucher_limit == 1) {
            // 1: Áp dụng cho tất cả người dùng
            return true;
        } elseif ($coupon->user_voucher_limit == 2) {
            // 2: Chỉ áp dụng cho người dùng cụ thể
            return $coupon->users->contains($user->id);
        } elseif ($coupon->user_voucher_limit == 3) {
            // 3: Áp dụng theo giới tính
            return $user->gender === ($coupon->gender == 'male' ? 'male' : 'female');
        }

        return false;
    }
}
