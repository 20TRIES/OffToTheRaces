<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacePerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_horse_performances', function (Blueprint $table) {
            $table->bigInteger('race_id', false, true);
            $table->foreign('race_id')
                ->references('id')
                ->on('races')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->bigInteger('horse_id', false, true);
            $table->foreign('horse_id')
                ->references('id')
                ->on('horses')
                ->onDelete('RESTRICT')
                ->onUpdate('CASCADE');
            $table->smallInteger('seconds_to_finish', false, true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_horse_performances');
    }
}
