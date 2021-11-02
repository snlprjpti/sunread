<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("website_id");
            $table->unsignedBigInteger("store_id");
            $table->unsignedBigInteger("customer_id")->nullable();
            $table->string("store_name");
            $table->boolean("is_guest")->default(0);
            $table->unsignedBigInteger("billing_address_id");
            $table->unsignedBigInteger("shipping_address_id");
            $table->string("shipping_method");
            $table->string("shipping_method_label");
            $table->string("payment_method");
            $table->string("payment_method_label");
            $table->string("currency_code");
            $table->string("coupon_code")->nullable();
            $table->decimal("discount_amount")->nullable();
            $table->decimal("discount_amount_tax")->nullable();
            $table->decimal("shipping_amount")->nullable();
            $table->decimal("shipping_amount_tax")->nullable();
            $table->decimal("sub_total");
            $table->decimal("sub_total_tax_amount");
            $table->decimal("tax_amount");
            $table->decimal("grand_total");
            $table->decimal("weight")->nullable();
            
            $table->decimal("total_tax_amount");
            $table->decimal("total_discount_amount");
            $table->decimal("total_discount_amount_tax");

            $table->decimal("total_items_ordered")->nullable();
            $table->decimal("total_qty_ordered")->nullable();
            $table->string("customer_email");
            $table->string("customer_first_name");
            $table->string("customer_middle_name")->nullable();
            $table->string("customer_last_name");
            $table->string("customer_phone");
            $table->string("customer_taxvat")->nullable();
            $table->string("customer_ip_address")->nullable();
            $table->string("status");

            $table->foreign("website_id")->references("id")->on("websites");
            $table->foreign("store_id")->references("id")->on("stores");
            $table->foreign("customer_id")->references("id")->on("customers");
            $table->foreign("billing_address_id")->references("id")->on("customer_addresses");
            $table->foreign("shipping_address_id")->references("id")->on("customer_addresses");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
