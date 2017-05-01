<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->string('member_id', 8);
            $table->string('full_name', 100);
            $table->string('email', 50)->nullable();
            $table->date('dob')->nullable();
            $table->date('member_since')->nullable();
            $table->string('address', 100);
            $table->integer('region_id')->unsigned()->comment('Kabupaten/Kota tempat tinggal');
            $table->string('phone', 20);
            $table->integer('branch_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->primary('member_id');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
