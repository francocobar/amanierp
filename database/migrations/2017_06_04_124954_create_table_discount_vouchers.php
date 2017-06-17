<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscountVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by')->unsigned();
            $table->string('voucher_name', 30);
            $table->integer('voucher_qty')->unsigned();
            $table->tinyInteger('voucher_type')->unsigned()->comment('1. member 2. non member 3. member dan non member')
                    ->default(3);
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
        Schema::dropIfExists('discount_vouchers');
    }
}
