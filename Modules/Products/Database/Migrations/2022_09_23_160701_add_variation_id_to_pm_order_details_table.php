<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariationIdToPmOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('variation_id')->after('product_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('pm_product_variations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variation_id');
        });
    }
}
