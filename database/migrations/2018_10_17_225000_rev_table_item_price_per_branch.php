<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RevTableItemPricePerBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('item_id',6)->nullable()->default(null)->change();
        });
        Schema::table('item_prices', function (Blueprint $table) {
            $table->decimal('m_price', 15, 2)->after('branch_id');
            $table->decimal('nm_price', 15, 2)->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_prices', function (Blueprint $table) {
            //
        });
    }
}
