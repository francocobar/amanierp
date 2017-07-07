<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransactionHeaders4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            $table->string('customer_phone', 20)->nullable()->default(null)->after('member_id');
            $table->string('customer_name', 100)->nullable()->default(null)->after('member_id');
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
