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
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('hostname')->nullable();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('theme');
            $table->unsignedBigInteger('default_store_id');
            $table->unsignedBigInteger('default_currency_id');

            $table->foreign('default_store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('default_currency_id')->references('id')->on('currencies')->onDelete('cascade');
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
