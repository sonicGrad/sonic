<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('name')->nullable();
            $table->text('content')->nullable();
            $table->text('reply_msg')->nullable();
            $table->foreign('created_by')->references('id')->on('um_users');
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('cms_contact_us');
    }
}
