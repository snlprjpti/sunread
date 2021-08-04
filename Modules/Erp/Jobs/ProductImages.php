<?php

namespace Modules\Erp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Entities\ErpImport;
use Modules\Erp\Traits\HasErpMapper;

class ProductImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpMapper;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        if (env("ERP_IMAGE_MIGRATE"))
        {
            $this->storeImage();
        }
        else
        {
            $this->storeFromLocalImage();
        }
        ErpImport::whereType("productImages")->update(["status" => 1]);
    }
}
