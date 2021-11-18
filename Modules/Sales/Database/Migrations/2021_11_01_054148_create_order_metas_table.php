<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderMetasTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_metas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id");
            $table->string("meta_key");
            $table->text("meta_value");

            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_metas');
    }
}
