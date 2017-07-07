<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePembukuanBranches4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembukuan_branches', function (Blueprint $table) {
            $table->longText('description')->after('modal_total')->nullable()->default(null)->change();
            $table->longText('turnover_description')->after('turnover')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembukuan_branches', function (Blueprint $table) {
            //
        });
    }
}
