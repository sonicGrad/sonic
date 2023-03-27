<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductIdToUmRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->after('user_id')->nullable();
            $table->foreign('product_id')->references('id')->on('pm_products');
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
            $table->dropConstrainedForeignId('product_id');
        });
    }
}
