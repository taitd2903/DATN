<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id(); // Khóa chính tự động tăng
            $table->string('code', 255)->unique(); // Mã giảm giá, đảm bảo duy nhất
            $table->text('description')->nullable(); // Mô tả chi tiết về coupon
            $table->integer('discount_type')->default(1) // Loại giảm giá
                ->comment('1: Phần trăm, 2: Giá trị cố định');
            $table->decimal('discount_value', 12, 0); // Giá trị giảm giá (số tiền hoặc %)
            $table->date('start_date'); // Ngày bắt đầu có hiệu lực
            $table->date('end_date'); // Ngày kết thúc
            $table->integer('usage_limit'); // Tổng số lần coupon có thể sử dụng
            $table->integer('used_count')->default(0); // Số lần đã sử dụng
            $table->integer('usage_per_user'); // Giới hạn số lần sử dụng cho mỗi người dùng
            $table->integer('status')->default(1) // Trạng thái hoạt động
                ->comment('1: Hoạt động, 2: Dừng hoạt động');
            $table->integer('max_discount_amount')->nullable(); // Giảm tối đa (nếu là %)
            $table->integer('user_voucher_limit')->default(1) // Loại đối tượng áp dụng
                ->comment('1: Tất cả; 2: Người cụ thể; 3: Giới tính');
            $table->string('title', 255); // Tiêu đề coupon
            $table->string('gender')->nullable()->comment('male: Nam, female: Nữ');
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
