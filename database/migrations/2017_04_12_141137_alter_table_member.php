<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('place_of_birth', 20)->after('dob')->nullable();
            $table->string('stay_at', 20)->after('address')->nullable();
            $table->dropColumn(['region_id']);
            // $table->integer('region_id')->nullable()->comment('tempat tinggal kabupaten/kota')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('=members', function (Blueprint $table) {
            //
        });
    }
}
