<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValueToVnCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_coupons', function (Blueprint $table) {
            $table->float('value')->after('status')->default(0);
            $table->json('description')->after('value')->nullable();
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
            $table->dropColumn('value');
            $table->dropColumn('description');
        });
    }
}
