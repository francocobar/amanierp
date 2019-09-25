<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeaderOngoingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_ongoings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('member_id', 8)->nullable()->default(null)->comment('null kalau umum');
            // $table->decimal('sub_total_harga_item', 15, 2)
            //         ->comment('total harga item sebelum item didiskon');
            // $table->decimal('sub_total_diskon_item', 15, 2)
            //         ->comment('total diskon harga item');
            // $table->decimal('sub_total', 15, 2)
            //         ->comment('total sub_total_harga_item-sub_total_diskon_item');
            // $table->decimal('discount2', 15, 2)
            //         ->comment('diskon dihitung berdasarkan subtotal, wajib menggunakan voucher kode');
            // $table->decimal('total', 15, 2)
            //         ->comment('sub_total-discount2');
            $table->boolean('already_finish')->default(false);
            $table->integer('cashier_user_id')->unsigned()
                    ->comment('user id kasir');
            $table->softDeletes();
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
        Schema::dropIfExists('header__ongoings');
    }
}
