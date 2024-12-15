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
        Schema::create('tax_collectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('data_id'); // Foreign key
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile');
            $table->string('password_reset_token', 60)->nullable();
            $table->rememberToken(); // This is for "remember me" functionality
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_collectors');
    }
};
