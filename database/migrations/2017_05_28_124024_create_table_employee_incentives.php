<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmployeeIncentives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_incentives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('detail_id')->unsigned();
            $table->string('employee_id', 8);
            $table->decimal('incentive', 15, 2)->default(0);
            $table->integer('set_by')->unsigned()->default(0)->comment('kalau 0 berarti by sistem, kalau gak berarti set manual by user');
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
        Schema::dropIfExists('employee_incentives');
    }
}
