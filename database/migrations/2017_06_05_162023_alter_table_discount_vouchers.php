<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDiscountVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_vouchers', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->unsigned()->default(2)->after('voucher_qty');
            $table->decimal('discount_value',15, 2)->unsigned()->default(10000)->after('voucher_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_vouchers', function (Blueprint $table) {
            //
        });
    }
}
