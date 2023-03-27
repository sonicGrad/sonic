<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderDriverReachTimeToPmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_orders', function (Blueprint $table) {
            $table->timestamp('order_driver_reach_time')->nullable();
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
            $table->dropColumn('order_driver_reach_time');
        });
    }
}
