<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailVariablesTable extends Migration
{
    public function up(): void
    {
        Schema::create('email_variables', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("value");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_variables');
    }
}
