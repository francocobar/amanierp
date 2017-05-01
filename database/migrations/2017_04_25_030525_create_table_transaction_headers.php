<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransactionHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->char('invoice_id',22)->comment('INV/{prefix}/YYYYMMDD/XXXXX');
            $table->integer('branch_id')->unsigned();
            $table->integer('cashier_user_id')->unsigned();
            $table->string('member_id', 8)->nullable()->default(null)->comment('null kalau umum');
            $table->decimal('grand_total_item_price', 15, 2)->comment('total sblm diskon');
            $table->decimal('total_item_discount', 15, 2)->nullable()->default(null)->comment('total discount per item');
            $table->decimal('discount_total_input', 15, 2)->nullable()->default(null)->comment('discount input dari total');
            $table->tinyInteger('discount_total_type')->unsigned()->nullable()->default(null)->comment('1: input persen 2: input nilai pasti lgsg');
            $table->decimal('discount_total_fixed_value', 15, 2)->default(0)->comment('discount fixed value dari total');
            $table->tinyInteger('payment_type')->unsigned()->default(1)->comment('1: Tunai 2: Kredit 3: Credit Card 4: Debit Card');
            $table->decimal('total_paid', 15, 2)->comment('total yang dibayarkan');
            $table->decimal('change', 15, 2)->default(0)->comment('kembalian = total_paid-(grand_total_item_price-total_item_discount-discount_total_fixed_value)');
            $table->decimal('debt', 15, 2)->default(0)->comment('hutang sama kayak kembalian');
            $table->boolean('is_debt')->default(false)->comment('kalo ngutang jadi true');
            $table->integer('cashier2_user_id')->unsigned()->nullable()->default(null)->comment('cashier yg ubah status utang jadi lunas');
            $table->decimal('total_paid2', 15, 2)->default(0)->comment('total yang dibayarkan waktu pelunasan hutang');
            $table->decimal('change2', 15, 2)->default(0)->comment('kembalian pas pelunasan utang=total_paid2-debt');
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
        Schema::dropIfExists('transaction_headers');
    }
}
