<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePembukuanPusat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembukuan_pusat', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('modal', 15, 2)->comment('modal beli total');
            $table->decimal('turnover', 15, 2)->comment('omset');
            $table->string('description');
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
        Schema::dropIfExists('pembukuan_pusat');
    }
}
