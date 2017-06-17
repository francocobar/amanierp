<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePembukuanBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembukuan_branches', function (Blueprint $table) {
            $table->char('item_id',6)->after('detail_id')->nullable(null)->default(null);
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
