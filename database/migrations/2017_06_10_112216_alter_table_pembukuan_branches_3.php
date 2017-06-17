<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePembukuanBranches3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembukuan_branches', function (Blueprint $table) {
            $table->string('description')->after('modal_total')->nullable()->default(null)->change();
            $table->string('turnover_description')->after('turnover')->nullable()->default(null);
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
