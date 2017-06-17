<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBranchModalLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_modal_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->unsigned();
            $table->tinyInteger('modal_type')->unsigned()
                    ->comment('1. modal produk (auto pas stock dari pusat masuk diapproved) 2. modal input manual');
            $table->decimal('modal_value', 15, 2);
            $table->string('information', 100);
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
        Schema::dropIfExists('branch_modal_logs');
    }
}
