<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->char('item_id', 6);
            $table->string('item_name', 100);
            $table->string('description', 200)->nullable();
            $table->decimal('m_price', 15, 2);
            $table->decimal('nm_price', 15, 2);
            $table->integer('created_by')->unsigned();
            $table->primary('item_id');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
