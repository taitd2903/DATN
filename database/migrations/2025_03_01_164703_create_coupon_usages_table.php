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
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id(); // Khóa chính tự động tăng
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Khóa ngoại liên kết với bảng users
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade'); // Khóa ngoại liên kết với bảng coupons
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // Khóa ngoại liên kết với bảng orders (nullable)
            $table->timestamp('used_at'); // Thời gian mã giảm giá được sử dụng
            $table->timestamps(); // Thời gian tạo và cập nhật bản ghi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};