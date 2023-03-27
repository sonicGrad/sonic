<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToUmRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->after('user_id')->nullable();
            $table->foreign('order_id')->references('id')->on('pm_orders');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('um_ratings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
        });
    }
}
