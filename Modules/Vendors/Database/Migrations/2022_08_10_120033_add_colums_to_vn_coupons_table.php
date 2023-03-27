<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToVnCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_coupons', function (Blueprint $table) {
            $table->date('starting_data')->after('description')->nullable();
            $table->date('ended_data')->after('starting_data')->nullable();
            $table->unsignedBigInteger('type_id')->after('ended_data')->nullable();
            $table->foreign('type_id')->references('id')->on('vn_coupons_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vn_coupons', function (Blueprint $table) {
            $table->dropColumn('starting_data');
            $table->dropColumn('ended_data');
            $table->dropConstrainedForeignId('type_id');
        });
    }
}
