<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');  
            $table->string('customer_phone');  
            $table->text('customer_address');  
            
            $table->unsignedBigInteger('user_id');
            $table->text('note')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->enum('payment_method', ['cod', 'vnpay']);
            $table->enum('status', ['Chờ xác nhận', 'Đang giao', 'Hoàn thành', 'Hủy'])->default('Chờ xác nhận'); // Trạng thái đơn hàng
            $table->enum('payment_status', ['Chưa thanh toán', 'Đã thanh toán', 'Hoàn tiền'])->default('Chưa thanh toán'); // Trạng thái thanh toán
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};