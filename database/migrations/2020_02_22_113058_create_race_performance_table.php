<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacePerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_performance', function (Blueprint $table) {
            $table->bigInteger('horse_id', false, true);
            $table->foreign('horse_id')
                ->references('id')
                ->on('horses')
                ->onDelete('RESTRICT')
                ->onUpdate('CASCADE');
            $table->bigInteger('race_id', false, true);
            $table->foreign('race_id')
                ->references('id')
                ->on('races')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->smallInteger('time_to_finish', false, true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_performance');
    }
}