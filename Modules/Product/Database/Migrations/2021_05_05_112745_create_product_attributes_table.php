<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAttributesTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("attribute_id");
            $table->foreign("attribute_id")->references("id")->on("attributes")->onDelete("cascade");
            $table->unsignedBigInteger("channel_id")->nullable();
            $table->foreign("channel_id")->references("id")->on("channels")->onDelete("cascade");
            $table->unsignedBigInteger("product_id");
            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
            $table->unsignedBigInteger("store_id")->nullable();
            $table->foreign("store_id")->references("id")->on("stores")->onDelete("cascade");

            $table->string("value_type");
            $table->unsignedBigInteger("value_id")->nullable();

            $table->unique(["attribute_id", "channel_id", "product_id", "store_id", "value_type"], "attribute_compound_unique");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
}
