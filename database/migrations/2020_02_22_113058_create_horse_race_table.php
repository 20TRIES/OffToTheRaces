<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorseRaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horse_race', function (Blueprint $table) {
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
        Schema::dropIfExists('horse_race');
    }
}
