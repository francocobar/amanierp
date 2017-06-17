<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransferStocks2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_stocks', function (Blueprint $table) {
            $table->decimal('approved_stock', 5, 1)->nullable()->default(null)->after('stock');
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
