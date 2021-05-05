<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlRewritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_rewrites', function (Blueprint $table) {
            $table->id();
            $table->string("request_path");
            $table->string("entity_controller");
            $table->string("entity_method");
            $table->unsignedBigInteger("entity_id");
            $table->foreign('entity_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('url_rewrites');
    }
}
