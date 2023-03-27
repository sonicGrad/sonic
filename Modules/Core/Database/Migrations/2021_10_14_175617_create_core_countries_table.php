<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_countries', function (Blueprint $table) {
            $table->id();

            $table->json('name')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // $table->foreign('created_by')->references('id')->on('um_users');
        });
        
        Schema::create('core_country_provinces', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('country_id');
            $table->json('name')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('core_countries');
            // $table->foreign('created_by')->references('id')->on('um_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_country_provinces');
        Schema::dropIfExists('core_countries');
    }
}
