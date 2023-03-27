<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDriverIdToPmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->after('buyer_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('dr_drivers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');
        });
    }
}
