<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('navigation_menu_id');
            $table->unsignedBigInteger('website_id');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('navigation_menu_id')->references('id')->on('navigation_menus')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('website_id')->references('id')->on('websites')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigation_menu_items');
    }
}
