<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("products")->onDelete("cascade");
            $table->unsignedBigInteger("brand_id")->nullable();
            $table->foreign("brand_id")->references("id")->on("brands")->onDelete("restrict");
            $table->unsignedBigInteger("attribute_group_id")->nullable();
            $table->foreign("attribute_group_id")->references("id")->on("attribute_groups")->onDelete("restrict");

            $table->string("sku")->unique();
            $table->string("type")->default("simple");
            $table->boolean("status")->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("products");
    }
}
