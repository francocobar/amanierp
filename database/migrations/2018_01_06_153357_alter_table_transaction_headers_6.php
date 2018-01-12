<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransactionHeaders6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            $table->tinyInteger('status')->unsigned()->comment('1: sedang berlangsung
                2:selesai 3:dibatalkan')->default(1)->after('id');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->tinyInteger('status')->unsigned()->comment('1: sedang berlangsung
                2:selesai 3:dibatalkan 0:pending,
                jika header selesai detail yg sedang-berlangsung otomatis selesai')->default(1)
                ->after('id');
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
