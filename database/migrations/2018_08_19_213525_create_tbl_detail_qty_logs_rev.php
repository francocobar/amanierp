<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDetailQtyLogsRev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_qty_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('detail_id')->unsigned()->nullable()->default(null);
            $table->integer('log_by')->unsigned()->nullable()->default(null);
            $table->string('log_text', 500);
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
        Schema::dropIfExists('detail_qty_logs');
    }
}
