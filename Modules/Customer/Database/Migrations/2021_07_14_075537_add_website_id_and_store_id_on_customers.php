<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebsiteIdAndStoreIdOnCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('website_id');
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');

            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            $table->unique([ "email", "website_id" ], 'customer_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('website_id');
            $table->dropForeign('website_id');
            $table->dropColumn('store_id');
            $table->dropForeign('store_id');
        });
    }
}
