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
        Schema::table('order_returns', function (Blueprint $table) {
            $table->string('refund_image')->nullable()->after('bank_name');
            $table->text('refund_note')->nullable()->after('refund_image');
            $table->timestamp('refunded_at')->nullable()->after('refund_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['refund_image', 'refund_note', 'refunded_at']);
        });
    }
};
