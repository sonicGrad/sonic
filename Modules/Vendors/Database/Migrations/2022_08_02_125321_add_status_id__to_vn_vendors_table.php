<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToVnVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('type_id');
            $table->foreign('status_id')->references('id')->on('vn_vendors_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vn_vendors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');

        });
    }
}
