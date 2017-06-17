<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransferStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_stocks', function (Blueprint $table) {
            $table->decimal('modal_cabang', 15, 2)->after('harga jual pusat ke cabang')->after('item_id');
            $table->decimal('modal_pusat', 15, 2)->after('harga/modal si pusat')->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_stocks', function (Blueprint $table) {
            //
        });
    }
}
