<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToPmProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_products', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('quantity');
            $table->foreign('status_id')->references('id')->on('pm_products_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');

        });
    }
}
