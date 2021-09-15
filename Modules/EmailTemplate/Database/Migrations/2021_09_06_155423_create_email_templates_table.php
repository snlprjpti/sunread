<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplatesTable extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->string("subject");
            $table->longtext("content");
            $table->string("style")->nullable();
            $table->boolean("is_default")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
}
