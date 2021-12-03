<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMetaValueColumnOrderMetasTable extends Migration
{
    public function up(): void
    {
        Schema::table('order_metas', function (Blueprint $table) {
            $table->json("meta_value")->change();
        });
    }
    
    public function down(): void
    {
        Schema::table('order_metas', function (Blueprint $table) {

        });
    }
}
