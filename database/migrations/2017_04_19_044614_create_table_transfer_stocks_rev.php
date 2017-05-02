<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransferStocksRev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->char('item_id', 6)->comment('item yg mau ditransfer');
            $table->integer('branch_id')->unsigned()->comment('branch yang dituju');
            $table->integer('stock')->comment('stock yg mau ditransfer ke cabang');
            $table->integer('sender')->unsigned()->comment('id superadmin yg mengirim');
            $table->integer('approval')->unsigned()->comment('id manager yg menyatakan diterima/ditolak');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('1: pending 2: accepted 3: rejected');
            $table->dateTime('approval_date')->nullable()->default(null);
            $table->string('sender_note', 100)->comment('catatan dari pengirim')->nullable()->default(null);
            $table->string('approval_note', 100)->comment('catatan dari approval')->nullable()->default(null);
            $table->timestamps();

        });

        // Schema::create('renting_datas', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('transaction_id')->unsigned();
        //     $table->char('item_id', 6)->comment('item yg disewa');
        //     $table->integer('branch_id')->unsigned()->comment('tempat cabang pengambilan');
        //     $table->dateTime('taken_date');
        //     $table->boolean('taken_status');
        //     $table->dateTime('return_date')->nullable()->default(null);
        //     $table->timestamps();

        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_stocks');
    }
}
