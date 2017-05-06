<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('employee_id', 8);
            $table->string('full_name', 100);
            $table->string('email', 50)->nullable();
            $table->date('dob')->nullable();
            $table->string('address', 100);
            $table->string('phone', 20);
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->primary('employee_id');
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
        Schema::dropIfExists('employees');
    }
}
