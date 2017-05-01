<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJasaConfigurationV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jasa_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configured_by')->unsigned();
            $table->char('item_id_jasa', 6);
            $table->char('item_id_produk', 6);
            $table->tinyInteger('pembilang')->unsigned();
            $table->tinyInteger('penyebut')->unsigned();
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
        Schema::dropIfExists('jasa_configurations');
    }
}
