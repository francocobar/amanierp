<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBranchStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_stock_logs', function (Blueprint $table) {
            $table->decimal('stock_before', 10, 7)->default(0)->change();
            $table->decimal('stock_new_input', 10, 7)->comment('stok yg di approved')->change();
            $table->decimal('stock_after', 10, 7)->comment('stok akhir(yg diinput ditambah yg lama)')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_stock_logs', function (Blueprint $table) {
            //
        });
    }
}
