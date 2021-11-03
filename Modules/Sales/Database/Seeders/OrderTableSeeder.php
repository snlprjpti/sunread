<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Website;

class OrderTableSeeder extends Seeder
{
    public function run():  void
    {
        $data = [
            "website_id" => 1,
            "store_id" => 1,
            "store_name" => "International Store",
            "shipping_method" => "FedEX",
            "shipping_method_label" => "fedex",
            "payment_method" => "Stripe",
            "payment_method_label" => "stripe",
            "currency_code" => "EUR",
            "sub_total" => 2000.00,
            "sub_total_tax_amount" => 1,
            "discount_amount" => 1,
            "discount_amount_tax" => 1,
            ""
        ];

        DB::table("orders")->insert($data);
    }
}
