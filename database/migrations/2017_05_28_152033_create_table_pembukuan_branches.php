<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePembukuanBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembukuan_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('detail_id')->unsigned()->nullable()->default(null);
            $table->integer('branch_id')->unsigned();
            $table->decimal('modal_per_qty_item', 15, 2)->default(0);
            $table->tinyInteger('qty_item');
            $table->decimal('modal_total', 15, 2)->default(0);
            $table->decimal('turnover', 15, 2)->default(0);
            $table->decimal('profit', 15, 2);
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
        Schema::dropIfExists('pembukuan_branches');
    }
}
