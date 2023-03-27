<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_drivers', function (Blueprint $table) {
            $table->id();
            $table->json('location')->nullable();
            $table->string('driving_license_no')->nullable();
            $table->date('driving_license_ended')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('deactivated')->nullable();
            $table->foreign('user_id')->references('id')->on('um_users');
            $table->foreign('created_by')->references('id')->on('um_users');
            $table->foreign('type_id')->references('id')->on('dr_drivers_types');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dr_drivers');
    }
}
