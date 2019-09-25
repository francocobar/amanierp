<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Payments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_code')->unsigned()
                    ->comment('payment code is integer');
            $table->decimal('nominal_to_pay', 15, 2)
                    ->comment('total yg ingin dibayarkan');
            $table->decimal('cash_given', 15, 2)
                ->comment('uang yg diserahkan');
            $table->decimal('change', 15, 2)
                ->comment('uang kembalian jika cash_given lbh dari nominal_to_pay');
            $table->integer('payment_method')->unsigned()
                    ->comment('1: cash, 2: kartu debit, 3: kartu kredit');
            $table->string('card_number', 16)->nullable()->default(null);
            $table->integer('branch_id')->unsigned()
                    ->comment('branch tempat payment dilakukan');
            $table->integer('cashier_user_id')->unsigned();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
