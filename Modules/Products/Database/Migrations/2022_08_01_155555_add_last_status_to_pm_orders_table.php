<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastStatusToPmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('last_status')->nullable();
            $table->foreign('last_status')->references('id')->on('pm_order_state')->after('price');

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
            $table->dropConstrainedForeignId('last_status');
        });
    }
}
