<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('Chờ xác nhận', 'Đang giao', 'Hoàn thành', 'Hủy', 'Đã giao hàng thành công') DEFAULT 'Chờ xác nhận'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('Chờ xác nhận', 'Đang giao', 'Hoàn thành', 'Hủy') DEFAULT 'Chờ xác nhận'");
    }
};
