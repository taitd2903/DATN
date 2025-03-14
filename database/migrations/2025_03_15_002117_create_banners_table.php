<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image'); // Lưu đường dẫn ảnh
            $table->string('link')->nullable(); // Link điều hướng
            $table->boolean('is_active')->default(false); // Trạng thái hoạt động
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('banners');
    }
};