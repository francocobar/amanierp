<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNextPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('next_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('header_id')->unsigned();
            $table->decimal('debt_before', 15, 2);
            $table->decimal('paid_value', 15, 2)->comment('yang mau dibayarkan');
            $table->decimal('total_paid', 15, 2)->comment('yg disrahkan ke kasir');
            $table->decimal('change', 15, 2)->default(0)->comment('total_paid-paid_value');
            $table->decimal('debt_after', 15, 2)->default(0)->comment('debt_before-paid_value');
            $table->tinyInteger('payment_type')->unsigned()->default(1)->comment('1: Tunai 3: Credit Card 4: Debit Card');
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
        Schema::dropIfExists('next_payments');
    }
}
