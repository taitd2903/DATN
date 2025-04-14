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
            $table->string('bank_account')->nullable()->after('image');
            $table->string('account_holder')->nullable()->after('bank_account');
            $table->string('bank_name')->nullable()->after('account_holder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['bank_account', 'account_holder', 'bank_name']);
        });
    }
};
