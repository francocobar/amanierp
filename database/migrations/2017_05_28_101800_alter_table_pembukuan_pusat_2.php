<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePembukuanPusat2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembukuan_pusat', function (Blueprint $table) {
            $table->integer('branch_buyer')->nullable()->default(null)->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembukuan_pusat', function (Blueprint $table) {
            //
        });
    }
}
