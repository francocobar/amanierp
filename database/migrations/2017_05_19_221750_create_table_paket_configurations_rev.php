<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaketConfigurationsRev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paket_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configured_by')->unsigned();
            $table->char('item_id_paket', 6);
            $table->char('item_id_jasa', 6);
            $table->tinyInteger('qty_jasa')->unsigned();
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
        Schema::dropIfExists('paket_configutations');
    }
}
