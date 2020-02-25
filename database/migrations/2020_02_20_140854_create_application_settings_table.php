<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\ApplicationSetting\ApplicationSettingModel;
use Carbon\Carbon;
use App\Lib\DateTime\Format;

class CreateApplicationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->string('id');
            $table->string('value');
            $table->timestamps();
            $table->index('id');
        });


        // Initialize application time
        ApplicationSettingModel::create(['id' => 'time', 'value' => Carbon::now()->format(Format::DEFAULT)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_settings');
    }
}
