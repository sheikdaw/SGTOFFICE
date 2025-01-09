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
        Schema::create('attendences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->Date('Data');
            $table->time('in_time');
            $table->time('out_time')->nullable();
            $table->string('ward');
            $table->json('inlocation')->nullable();
            $table->json('outlocation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendences');
    }
};
