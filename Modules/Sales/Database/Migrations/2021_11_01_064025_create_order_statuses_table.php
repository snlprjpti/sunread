<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusesTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
}
