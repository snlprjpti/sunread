<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    public function up(): void
    {
        Schema::create("stores", function (Blueprint $table) {
            $table->id();
            $table->string("currency");
            $table->string("name");
            $table->string("slug");
            $table->string("locale");
            $table->string("image")->nullable();
            $table->foreign("currency")->references("code")->on("currencies")->onUpdate("cascade");
            $table->boolean("status")->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("stores");
    }
}
