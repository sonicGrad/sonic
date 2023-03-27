<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUmOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('um_otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_no')->nullable();
            $table->string('code')->nullable();
            $table->enum('verify', [0,1])->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('message');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('user_id')->references('id')->on('um_users');
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
        Schema::dropIfExists('um_otps');
    }
}
