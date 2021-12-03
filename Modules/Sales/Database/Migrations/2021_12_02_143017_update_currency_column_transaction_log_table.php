<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCurrencyColumnTransactionLogTable extends Migration
{
    public function up(): void
    {
        Schema::table('order_transaction_logs', function (Blueprint $table) {
            $table->string("currency")->change();
            $table->json("request")->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_transaction_logs', function (Blueprint $table) {

        });
    }
}
