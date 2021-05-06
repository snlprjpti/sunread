<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('hostname')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('theme')->nullable();
            $table->unsignedBigInteger('default_store_id')->nullable();
            $table->string('default_currency')->nullable();
            $table->unsignedBigInteger("website_id");
            $table->foreign('default_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('default_currency')->references('code')->on('currencies')->onUpdate('cascade');
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
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
        Schema::dropIfExists('channels');
    }
}
