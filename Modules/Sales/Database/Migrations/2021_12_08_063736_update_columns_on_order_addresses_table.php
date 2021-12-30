<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnsOnOrderAddressesTable extends Migration
{

    public function up(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            
            $table->string('address3')->nullable()->after('address_line_2');
            $table->renameColumn('address_line_1', 'address1');
            $table->renameColumn('address_line_2', 'address2');
            $table->renameColumn('postal_code', 'postcode');
        });
    }

    public function down(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {

        });
    }
}
