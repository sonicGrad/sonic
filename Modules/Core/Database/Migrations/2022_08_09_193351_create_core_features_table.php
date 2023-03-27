<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('typeable_id')->nullable();
            $table->string('typeable_type')->nullable();
            $table->unsignedBigInteger('feature_type')->nullable();
            $table->enum('status', ['1','2'])->default('1');
            $table->date('stating_date')->nullable();
            $table->date('ended_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('feature_type')->references('id')->on('core_types_of_features');
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
        Schema::dropIfExists('core_features');
    }
}
