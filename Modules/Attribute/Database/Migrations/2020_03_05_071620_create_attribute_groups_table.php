<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeGroupsTable extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_family_id');
            $table->foreign('attribute_family_id')->references('id')->on('attribute_families')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('position')->nullable();
            $table->boolean('is_user_defined')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_groups');
    }
}
