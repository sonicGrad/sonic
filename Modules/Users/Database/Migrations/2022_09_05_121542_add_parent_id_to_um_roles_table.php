<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdToUmRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->after('label')->nullable();
            $table->foreign('parent_id')->references('id')->on('um_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('um_roles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
}
