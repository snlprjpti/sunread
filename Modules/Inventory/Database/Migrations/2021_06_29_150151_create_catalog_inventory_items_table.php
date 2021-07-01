<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogInventoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->integer("order_id");
            $table->integer("adjusted_by");
            $table->integer("quantity");
            $table->string("event")->nullable();
            $table->string("adjustment_type")->nullable();

            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
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
        Schema::dropIfExists('catalog_inventory_items');
    }
}
