<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInputStockLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_stock_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->char('item_id', 6);
            $table->decimal('modal_per_pcs_before', 15, 2)->default(0);
            $table->integer('stock_before')->default(0);
            $table->decimal('modal_new_input', 15, 2)->comment('modal per pcs yg baru diinput');
            $table->integer('stock_new_input')->comment('stok yg diipunt');
            $table->decimal('modal_per_pcs_after', 15, 2)->comment('modal per pcs rata2 (akhir)');
            $table->integer('stock_after')->comment('stok akhir(yg diinput ditambah yg lama)');
            $table->integer('input_by')->unsigned();
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
        Schema::dropIfExists('input_stock_logs');
    }
}
