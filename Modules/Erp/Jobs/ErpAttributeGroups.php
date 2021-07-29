<?php

namespace Modules\Erp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Traits\HasErpMapper;

class ErpAttributeGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpMapper;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $this->erpImport("attributeGroups", $this->url."webItemAttributeGroups");
    }
}
