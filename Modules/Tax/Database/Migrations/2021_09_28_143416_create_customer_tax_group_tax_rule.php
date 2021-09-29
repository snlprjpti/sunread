<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTaxGroupTaxRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_tax_group_tax_rule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("customer_tax_group_id");
            $table->unsignedBigInteger("tax_rule_id");
          
            $table->foreign("customer_tax_group_id")->references("id")->on("customer_tax_groups")->onDelete("cascade");
            $table->foreign("tax_rule_id")->references("id")->on("tax_rules")->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_tax_group_tax_rule');
    }
}
