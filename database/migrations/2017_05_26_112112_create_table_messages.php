<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('msg_type')->default(1)->comment('1: butuh content 2: ref ke trf stock ...');
            $table->string('subject');
            $table->string('content')->nullable()->default(null);
            $table->integer('sender')->unsigned()->default(0)->comment('0: sender sistem');
            $table->integer('receiver')->unsigned()->default(0)->comment('0: all admin');
            $table->boolean('read')->default(false);
            $table->integer('ref')->unsigned()->nullable()->default(null);
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
        Schema::dropIfExists('messages');
    }
}
