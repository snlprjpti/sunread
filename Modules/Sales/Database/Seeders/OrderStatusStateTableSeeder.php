<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusStateTableSeeder extends Seeder
{
    public function run(): void
    {
        $state_statuses = ["new", "pending", "processing", "holded", "completed", "closed", "cancelled"];
        foreach ($state_statuses as $status) {
            $data[] = [
                "name" => $status,
                "state" => Str::slug($status),
                "is_default" => 0,
                "position" => 1,
                "created_at" => now()
            ];
        }

        DB::table("order_status_states")->insert($data);
    }
}
