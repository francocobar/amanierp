<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDiscountVoucherList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_voucher_list', function (Blueprint $table) {
            $table->integer('header_id')->unsigned()->comment('ref to transaction_header')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_voucher_list', function (Blueprint $table) {
            //
        });
    }
}
