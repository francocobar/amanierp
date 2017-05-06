<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRentingDatasRev3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renting_datas', function (Blueprint $table) {
            $table->integer('taking_pic')->unsigned()->nullable()->default(null)->comment('cashier saat pengambilan')
                    ->after('taking_date');
            $table->integer('return_pic')->unsigned()->nullable()->default(null)->comment('cashier saat pengembalian')
                    ->after('return_date');

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
