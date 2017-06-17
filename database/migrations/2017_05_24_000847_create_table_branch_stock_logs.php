<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBranchStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_stock_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->unsigned();
            $table->char('item_id', 6);
            $table->decimal('modal_per_pcs_before', 15, 2)->default(0);
            $table->decimal('stock_before', 10, 1)->default(0);
            $table->decimal('modal_new_input', 15, 2)->comment('modal per pcs yg baru masuk/approved');
            $table->decimal('stock_new_input', 10, 1)->comment('stok yg di approved');
            $table->decimal('modal_per_pcs_after', 15, 2)->comment('modal per pcs rata2 (akhir)');
            $table->decimal('stock_after', 10, 1)->comment('stok akhir(yg diinput ditambah yg lama)');
            $table->integer('supplied_by')->unsigned();
            $table->integer('approved_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_stock_logs');
    }
}
