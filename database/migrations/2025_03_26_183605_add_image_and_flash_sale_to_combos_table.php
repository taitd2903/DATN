<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->string('image')->nullable()->after('discount_price');
            $table->boolean('is_flash_sale')->default(false)->after('image');
        });
    }
    
    public function down()
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn(['image', 'is_flash_sale']);
        });
    }
    
};
