<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationMenuItemValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_menu_item_values', function (Blueprint $table) {
            $table->id();
            $table->string('scope');
            $table->unsignedBigInteger('navigation_menu_item_id');
            $table->unsignedBigInteger('scope_id');
            $table->string('attribute');
            $table->string('value')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('navigation_menu_item_id')->references('id')->on('club_houses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigation_menu_item_values');
    }
}
