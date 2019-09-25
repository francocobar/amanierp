<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrxHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_headers', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('member_id', 8)->nullable()->default(null)->comment('null kalau umum');
            $table->string('customer_name', 255)->default(null)->nullable()
                    ->comment('customer name: member or guest');
            $table->decimal('sub_total_harga_item', 15, 2)
                    ->comment('total harga item sebelum item didiskon');
            $table->decimal('sub_total_diskon_item', 15, 2)
                    ->comment('total diskon harga item')->default(0);
            $table->decimal('sub_total', 15, 2)
                    ->comment('total sub_total_harga_item-sub_total_diskon_item');
            $table->decimal('discount2', 15, 2)
                    ->comment('diskon dihitung berdasarkan subtotal, wajib menggunakan voucher kode');
            $table->decimal('total', 15, 2)
                    ->comment('sub_total-discount2');
            $table->integer('cashier_user_id')->unsigned();
            $table->integer('branch_id')->unsigned()
                    ->comment('branch tempat trx dibuat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_headers');
    }
}
