<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTrxHeaders3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_headers', function (Blueprint $table) {
            $table->string('customer_phone', 20)->nullable()->default(null)->after('member_id');
            $table->string('customer_name', 255)->default(null)->nullable()
                        ->comment('customer name: member or guest')->after('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trx_headers', function (Blueprint $table) {
            //
        });
    }
}
