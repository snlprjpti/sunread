<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCacheManagementTable extends Migration
{
    public function up(): void
    {
        Schema::create('cache_management', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->unique();
            $table->string("description")->nullable();
            $table->string("tag")->unique()->nullable();
            $table->string("key")->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_management');
    }
}
