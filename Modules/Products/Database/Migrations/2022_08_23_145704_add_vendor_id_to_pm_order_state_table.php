<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendorIdToPmOrderStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_order_state', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->after('driver_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vn_vendors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_order_state', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
}
