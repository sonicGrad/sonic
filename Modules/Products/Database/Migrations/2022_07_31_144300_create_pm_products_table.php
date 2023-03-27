<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pm_products', function (Blueprint $table) {
            $table->id();
            $table->json('name')->nullable();
            $table->json('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->String('currency_id')->nullable();
            // $table->float('price');
            // $table->smallInteger('quantity')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('deactivated')->nullable();
            $table->foreign('category_id')->references('id')->on('pm_categories');
            $table->foreign('vendor_id')->references('id')->on('vn_vendors');
            $table->foreign('currency_id')->references('id')->on('core_currencies');
            $table->foreign('created_by')->references('id')->on('um_users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pm_products');
    }
}
