<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionDetailsRev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('header_id')->unsigned();
            $table->char('item_id', 6);
            $table->decimal('item_price', 15, 2)->comment('harga per item saat itu');
            $table->tinyInteger('item_qty')->comment('jumlah item dibeli');
            $table->decimal('item_total_price', 15, 2)->comment('item_price x item qty, dicatat buat gampang bikin report');
            $table->decimal('item_discount_input', 15, 2)->nullable()->default(null);
            $table->tinyInteger('item_discount_type')->unsigned()->nullable()->default(null)->comment('1: input persen 2: input nilai pasti lgsg');
            $table->decimal('item_discount_fixed_value', 15, 2)->default(0);
            $table->string('item_pic', 8)->nullable()->default(null)->comment('kalo jasa siapa pic nya');
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
        Schema::dropIfExists('transaction_details');
    }
}
