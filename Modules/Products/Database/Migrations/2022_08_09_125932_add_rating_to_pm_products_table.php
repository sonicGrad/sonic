<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingToPmProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_products', function (Blueprint $table) {
            $table->float('percentage_of_rating')->default(0)->after('status_id');
            $table->smallInteger('number_of_raters')->default(0)->after('percentage_of_rating');
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
            $table->dropColumn('percentage_of_rating');
            $table->dropColumn('number_of_raters');
        });
    }
}
