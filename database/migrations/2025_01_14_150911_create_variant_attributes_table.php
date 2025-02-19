<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ví dụ: 'size', 'color'
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('variant_attributes');
    }
};
