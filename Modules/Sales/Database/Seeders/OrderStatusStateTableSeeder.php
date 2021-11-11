<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusStateTableSeeder extends Seeder
{
    public function run(): void
    {
        $state_statuses = [
            "new" => "new" ,
            "pending_payment" => "pending",
            "processing" => "processing",
            "holded" => "holded",
            "completed" => "completed",
            "closed" => "closed",
            "cancelled" => "cancelled"
    ];
        foreach ($state_statuses as $state => $status) {
            $data[] = [
                "status" => $status,
                "state" => $state,
                "is_default" => 0,
                "position" => 1,
                "created_at" => now()
            ];
        }

        DB::table("order_status_states")->insert($data);
    }
}
