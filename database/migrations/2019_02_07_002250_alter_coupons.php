<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('max_fix_value', 15, 2)->default(null)->nullable()->comment('kalo null gak ada batesan')->after('coupon_code');
            $table->tinyInteger('disc_value_type')->default(1)->comment('1: persen, 2: fix')->after('coupon_code');
            $table->decimal('disc_value', 15, 2)->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
}
