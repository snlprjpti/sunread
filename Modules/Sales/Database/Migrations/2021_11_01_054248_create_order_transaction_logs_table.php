<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTransactionLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id");
            $table->decimal("amount");
            $table->decimal("currency");
            $table->string("ip_address")->nullable();
            $table->json("request");
            $table->json("response");
            $table->string("response_code", 20);

            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_transaction_logs');
    }
}
