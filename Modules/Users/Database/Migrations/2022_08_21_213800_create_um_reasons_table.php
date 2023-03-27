<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('um_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reasonable_id')->nullable();
            $table->string('reasonable_type')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->text('reason')->nullable();
            $table->foreign('order_id')->references('id')->on('pm_orders');
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
        Schema::dropIfExists('um_reasons');
    }
}
