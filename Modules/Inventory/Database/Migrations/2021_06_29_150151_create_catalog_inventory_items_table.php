<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogInventoryItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_id")->nullable();
            $table->integer("order_id")->nullable();
            $table->integer("adjusted_by")->nullable();
            $table->integer("quantity");
            $table->string("event")->nullable();
            $table->string("adjustment_type")->nullable();

            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
            $table->foreign("adjusted_by")->references("id")->on("admins")->onDelete("cascade");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_inventory_items');
    }
}
