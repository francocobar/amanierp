<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMigrationPembukuanBranches4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembukuan_branches', function (Blueprint $table) {
            $table->string('description', 200)->after('modal_total')->nullable()->default(null)->change();
            $table->string('turnover_description', 200)->after('turnover')->nullable()->default(null)->change();
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
