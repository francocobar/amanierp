<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_incentives', function (Blueprint $table) {
            $table->integer('branch_id')->unsgined()->after('incentive')->comment('branch yg bayar insentif');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_incentives', function (Blueprint $table) {
            //
        });
    }
}
