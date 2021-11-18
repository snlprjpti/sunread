<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusTableSeeder extends Seeder
{
    public function run(): void
    {
        $status = ["new", "pending", "processing", "holded", "completed", "closed", "cancelled"];
        foreach ($status as $row) {
            $data[] = [
                "name" => $row,
                "slug" => Str::slug($row),
                "created_at" => now()
            ];
        }

        DB::table("order_statuses")->insert($data);
    }
}
