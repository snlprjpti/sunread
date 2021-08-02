<?php

namespace Modules\Erp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Entities\ErpImport;
use Modules\Erp\Traits\HasErpMapper;

class ErpAttributeGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpMapper;

    public $skip_token;

    public function __construct(?string $skip_token = null)
    {
        $this->skip_token = $skip_token;
    }

    public function handle(): void
    {
        $this->erpImport("attributeGroups", $this->url."webItemAttributeGroups", $this->skip_token);
        ErpImport::whereType("attributeGroups")->update(["status" => 1]);

    }
}
