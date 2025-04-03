<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('product_variants', function (Blueprint $table) {
         
            $table->decimal('price', 15, 2)->change();
            $table->decimal('original_price', 15, 2)->change();
        });
    }

    public function down(): void {
        Schema::table('product_variants', function (Blueprint $table) {
            
            $table->decimal('price', 10, 2)->change();
            $table->decimal('original_price', 10, 2)->change();
        });
    }
};
