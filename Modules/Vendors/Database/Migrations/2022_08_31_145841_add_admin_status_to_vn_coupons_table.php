<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminStatusToVnCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_status')->after('status')->nullable();
            $table->foreign('admin_status')->references('id')->on('um_admin_status_for_vendor_activities');
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
            $table->dropConstrainedForeignId('admin_status');
        });
    }
}
