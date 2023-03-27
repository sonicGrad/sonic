<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToDrDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dr_drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('type_id');
            $table->foreign('status_id')->references('id')->on('dr_drivers_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dr_drivers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');

        });
    }
}
