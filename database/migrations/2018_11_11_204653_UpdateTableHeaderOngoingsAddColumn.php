<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableHeaderOngoingsAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('header_ongoings', function (Blueprint $table) {
            $table->integer('branch_id')->unsigned()
                    ->comment('user id kasir')->after('cashier_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('header_ongoings', function (Blueprint $table) {
        //     //
        // });
    }
}
