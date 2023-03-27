<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameToUmAddressBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('um_address_books', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->string('details')->after('name')->nullable();
            $table->string('mobile_no')->after('details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('um_address_books', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('details');
            $table->dropColumn('mobile_no');
        });
    }
}
