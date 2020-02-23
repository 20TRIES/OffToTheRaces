<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', 36);
            $table->string('name', 50);
            $table->decimal('base_speed', 3, 1);
            $table->decimal('speed_stat', 3, 1);
            $table->decimal('strength_stat', 3, 1);
            $table->decimal('endurance_stat', 3, 1);
            $table->timestamps();
            $table->unique('short_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horses');
    }
}
