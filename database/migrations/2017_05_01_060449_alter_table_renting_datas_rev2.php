<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRentingDatasRev2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renting_datas', function (Blueprint $table) {
            $table->dateTime('return_date')->after('renting_date')->nullable()->default(null)->comment('tanggal tanggal pengembalian');
            $table->dateTime('taking_date')->after('renting_date')->nullable()->default(null)->comment('tanggal pengambilan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renting_datas', function (Blueprint $table) {
            //
        });
    }
}
