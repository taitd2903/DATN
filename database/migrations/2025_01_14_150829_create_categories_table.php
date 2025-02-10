<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable(); // Cho phép null nếu là danh mục cha
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('categories');
    }
};
