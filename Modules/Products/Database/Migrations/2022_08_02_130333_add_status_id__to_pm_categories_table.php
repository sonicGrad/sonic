<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusIdToPmCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('vendor_type');
            $table->foreign('status_id')->references('id')->on('pm_categories_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');

        });
    }
}
