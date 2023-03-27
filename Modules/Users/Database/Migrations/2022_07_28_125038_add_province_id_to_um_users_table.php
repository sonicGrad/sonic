<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProvinceIdToUmUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_users', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->after('full_name')->nullable();
            $table->foreign('province_id')->references('id')->on('core_country_provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('um_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('province_id');
        });
    }
}
