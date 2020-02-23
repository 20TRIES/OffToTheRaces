<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', 36);
            $table->string('name', 50);
            $table->timestamp('finished_at')->nullable()->default(null);
            $table->smallInteger('length', false, true);
            $table->timestamps();
            $table->index('finished_at');
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
        Schema::dropIfExists('races');
    }
}
