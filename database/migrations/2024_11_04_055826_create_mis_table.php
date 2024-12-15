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
        Schema::create('mis', function (Blueprint $table) {
            $table->id();
            $table->string('assessment')->nullable();
            $table->string('old_assessment')->nullable();
            $table->string('number_floor')->nullable();
            $table->string('new_address')->nullable();
            $table->string('building_usage')->nullable();
            $table->string('construction_type')->nullable();
            $table->string('road_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('building_type')->nullable();
            $table->string('ward')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('old_door_no')->nullable();
            $table->string('new_door_no')->nullable();
            $table->string('plot_area')->nullable();
            $table->string('watertax')->nullable();
            $table->string('halfyeartax')->nullable();
            $table->string('balance')->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mis');
    }
};
