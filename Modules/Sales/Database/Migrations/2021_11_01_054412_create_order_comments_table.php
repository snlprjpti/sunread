<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCommentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("order_id");
            $table->unsignedBigInteger("user_id");
            $table->boolean("is_customer_notified")->default(0);
            $table->boolean("is_visible_on_storefornt")->default(0);

            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("admins");

            $table->text("comment");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_comments');
    }
}
