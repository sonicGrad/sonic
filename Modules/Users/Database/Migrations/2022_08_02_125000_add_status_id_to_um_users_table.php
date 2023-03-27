<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToUmUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_users', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('email');
            $table->foreign('status_id')->references('id')->on('um_users_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('um_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
    }
}
