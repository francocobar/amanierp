<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNextPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('next_payments', function (Blueprint $table) {
            $table->integer('cashier_user_id')->unsigned()->after('debt_after');
            $table->integer('branch_id')->unsigned()->after('debt_after');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('next_payments', function (Blueprint $table) {
            //
        });
    }
}
