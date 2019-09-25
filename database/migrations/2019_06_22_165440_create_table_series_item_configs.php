<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSeriesItemConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series_item_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('series_item_id')->unsigned();
            $table->integer('has_item_id')->unsigned();
            $table->integer('quota')->unsigned();
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
        Schema::dropIfExists('series_item_configs');
    }
}
