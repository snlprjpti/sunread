<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_houses', function (Blueprint $table) {
            $table->id();
            $table->integer('position');
            $table->unsignedBigInteger('website_id');
            $table->string('type');
            $table->boolean('status')->default(1);
            $table->timestamps();

            // Foreign Key
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_houses');
    }
}
