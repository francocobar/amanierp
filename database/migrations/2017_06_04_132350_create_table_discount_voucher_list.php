<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscountVoucherList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_voucher_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voucher_id')->unsigned();
            $table->string('voucher_code', 20);
            $table->dateTime('claimed_at')->nullable()->default(null);
            $table->integer('header_id')->unsigned()->comment('ref to transaction_header');
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
        Schema::dropIfExists('discount_voucher_list');
    }
}
