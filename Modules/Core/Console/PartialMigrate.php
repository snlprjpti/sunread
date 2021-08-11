<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PartialMigrate extends Command
{ 
    protected $signature = 'partial:migrate';

    protected $description = 'This command will run all updated seeders.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        Schema::disableForeignKeyConstraints();
        DB::table('products')->truncate();
        DB::table('attribute_configurable_products')->truncate();
        DB::table('attribute_configurable_products')->truncate();
        DB::table('categroy_product')->truncate();
        DB::table('channel_product')->truncate();
        DB::table('catalog_inventories')->truncate();
        DB::table('catalog_inventory_items')->truncate();     
        DB::table('product_attributes')->truncate();
        DB::table('product_attribute_boolean')->truncate();
        DB::table('product_attribute_decimal')->truncate();
        DB::table('product_attribute_integer')->truncate();
        DB::table('product_attribute_string')->truncate();
        DB::table('product_attribute_text')->truncate();
        DB::table('product_attribute_timestamp')->truncate();
        DB::table('product_images')->truncate();
        DB::table('product_tax_groups')->truncate();
        DB::table('attributes')->truncate();
        DB::table('attribute_groups')->truncate();
        DB::table('attribute_group_attributes')->truncate();
        DB::table('attribute_group_translations')->truncate();
        DB::table('attribute_translations')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->info("Values truncated");

        Artisan::call("migrate");
        $this->info("Migrated");

        Artisan::call("db:seed", ["--class" => "Modules\Attribute\Database\Seeders\AttributeTableSeeder"]);
        Artisan::call("db:seed", ["--class" => "Modules\Attribute\Database\Seeders\AttributeGroupTableSeeder"]);
        Artisan::call("db:seed", ["--class" => "Modules\Product\Database\Seeders\ProductTableSeeder"]);
        $this->info("Seeding completed");
        return true;
    }

}
