<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAddressesTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id");
            $table->unsignedBigInteger("customer_id")->nullable();
            $table->unsignedBigInteger("customer_address_id")->nullable();
            $table->string("address_type");
            $table->string("first_name");
            $table->string("middle_name")->nullable();
            $table->string("last_name");
            $table->string("phone");
            $table->string("email");
            $table->string("address_line_1");
            $table->string("address_line_2")->nullable();
            $table->string("postal_code");
            $table->unsignedBigInteger("country_id");
            $table->unsignedBigInteger("region_id")->nullable();
            $table->unsignedBigInteger("city_id")->nullable();
            $table->string("region_name")->nullable();
            $table->string("city_name")->nullable();
            $table->string("vat_number")->nullable();

            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");
            $table->foreign("customer_id")->references("id")->on("customers");
            $table->foreign("customer_address_id")->references("id")->on("customer_addresses");
            $table->foreign("country_id")->references("id")->on("countries");
            $table->foreign("region_id")->references("id")->on("regions");
            $table->foreign("city_id")->references("id")->on("cities");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
}
