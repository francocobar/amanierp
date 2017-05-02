<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransactionHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            $table->decimal('others', 15, 2)->default(0)->comment('biaya lain-lain')->after('total_item_discount');
            $table->dateTime('payment2_date')->nullable()->default(null)->comment('tanggal pelunasan')->after('change2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_headers', function (Blueprint $table) {
            //
        });
    }
}
