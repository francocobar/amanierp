<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransactionHeaders2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            $table->dropColumn(['change2', 'total_paid2', 'payment2_date','cashier2_user_id']);
            $table->dateTime('last_payment_date')->nullable()->default(null)->comment('tanggal pelunasan')->after('is_debt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            //
        });
    }
}
