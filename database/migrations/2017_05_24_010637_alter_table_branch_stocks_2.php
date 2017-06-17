<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBranchStocks2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_stocks', function (Blueprint $table) {
            $table->decimal('modal_per_pcs', 15, 2)->after('item_id');
            $table->decimal('stock', 10, 1)->default(0)->comment('stock cabang')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_stocks', function (Blueprint $table) {
            //
        });
    }
}
