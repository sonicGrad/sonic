<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdToVnVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->after('company_name')->nullable();
            $table->foreign('parent_id')->references('id')->on('vn_vendors');
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
            $table->dropConstrainedForeignId('parent_id');
        });
    }
}
