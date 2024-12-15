<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id(); // Creates an unsigned big integer for 'id'
            // Other columns...
            $table->string('corporation_id')->index();
            $table->string('corporation_name');
            $table->string('ward')->index();
            $table->string('zone')->index();
            $table->string('image');
            $table->string('polygon')->nullable();
            $table->string('line')->nullable();
            $table->string('point')->nullable();
            $table->string('polygondata')->nullable();
            $table->string('mis')->nullable();
            $table->string('qc')->nullable();
            $table->string('pointdata')->nullable();
            $table->string('extend_left')->nullable();
            $table->string('extend_right')->nullable();
            $table->string('extend_top')->nullable();
            $table->string('extend_bottom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data');
    }
}
