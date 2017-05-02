<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRentingDataRev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renting_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned()->comment('untuk cek invoice');
            $table->integer('branch_id')->unsigned()->comment('tempat ambil barang');
            $table->integer('item_id')->unsigned()->comment('tempat ambil barang');
            $table->integer('qty')->unsigned()->comment('jumlah disewa');
            $table->date('renting_date');
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
        Schema::dropIfExists('renting_datas');
    }
}
