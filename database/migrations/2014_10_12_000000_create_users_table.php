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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 15)->nullable();
            $table->string('image', 512)->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('address')->nullable(); 
            
            $table->string('ward')->nullable(); 
            $table->string('district')->nullable(); 
            $table->string('city')->nullable(); 
            $table->string('country')->default('Vietnam'); 
            $table->enum('role', ['user', 'admin', 'staff'])->default('user');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
