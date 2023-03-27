<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressBookIdToPmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_book_')->after('location')->nullable();
            $table->foreign('address_book_')->references('id')->on('um_address_books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_orders', function (Blueprint $table) {

        });
    }
}
