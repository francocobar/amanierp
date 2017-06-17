<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableModalLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modal_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('modal_type')->unsigned()
                    ->comment('1. modal produk (auto pas input stock pusat) 2. modal input manual');
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
        Schema::dropIfExists('modal_logs');
    }
}
