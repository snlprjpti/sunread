<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTableUpdated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_email')) 
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->string("customer_email")->nullable()->change();
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_first_name')) 
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->string("customer_first_name")->nullable()->change();
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_last_name')) 
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->string("customer_last_name")->nullable()->change();
            });
        }
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'customer_phone')) 
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->string("customer_phone")->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
