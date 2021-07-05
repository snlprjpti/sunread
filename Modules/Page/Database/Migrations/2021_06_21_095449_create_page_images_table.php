<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("page_id");
            $table->foreign("page_id")->references("id")->on("pages")->onDelete("cascade");

            $table->string('path');
            $table->boolean("is_banner")->default(0);
            $table->boolean("is_slider")->default(0);

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
        Schema::dropIfExists('page_images');
    }
}
