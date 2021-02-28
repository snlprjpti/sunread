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
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('timezone')->nullable();
            $table->string('hostname')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->unsignedBigInteger('default_locale_id');
            $table->unsignedBigInteger('base_currency_id');
            $table->foreign('default_locale_id')->references('id')->on('locales')->onDelete('cascade');
            $table->foreign('base_currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('channel_locales', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('locale_id');
            $table->primary(['channel_id', 'locale_id']);
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
            $table->foreign('locale_id')->references('id')->on('locales')->onDelete('cascade');
        });



        Schema::create('channel_currencies', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('currency_id');
            $table->primary(['channel_id', 'currency_id']);
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_currencies');

        Schema::dropIfExists('channel_locales');

        Schema::dropIfExists('channels');
    }
}
