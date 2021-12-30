<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("website_id");
            $table->unsignedBigInteger("store_id");
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("order_id");
            $table->json("product_options");
            $table->string("product_type");
            $table->string("sku");
            $table->string("name");
            $table->decimal("weight")->nullable();
            $table->decimal("qty");
            $table->decimal("cost");
            $table->decimal("price");
            $table->decimal("price_incl_tax");
            $table->string("coupon_code")->nullable();
            $table->decimal("discount_amount")->nullable();
            $table->decimal("discount_percent")->nullable();
            $table->decimal("discount_amount_tax")->nullable();
            $table->decimal("tax_amount");
            $table->decimal("tax_percent");
            $table->decimal("row_total");
            $table->decimal("row_total_incl_tax");
            $table->decimal("row_weight");

            $table->foreign("website_id")->references("id")->on("websites");
            $table->foreign("store_id")->references("id")->on("stores");
            $table->foreign("product_id")->references("id")->on("products");
            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}
