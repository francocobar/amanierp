<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableHeaderOngoingsAddColumn2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('header_ongoings', function (Blueprint $table) {
                $table->string('customer_name', 255)->default(null)->nullable()
                        ->comment('customer name: member or guest')->after('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('header_ongoings', function (Blueprint $table) {
            //
        });
    }
}
