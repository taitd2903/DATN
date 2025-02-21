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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->index()
                ->name('fk_cart_items_user_id'); 
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade')
                ->index()
                ->name('fk_cart_items_product_id'); 
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade')
                ->index()
                ->name('fk_cart_items_variant_id'); 
            $table->integer('quantity')->default(1)->unsigned();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
