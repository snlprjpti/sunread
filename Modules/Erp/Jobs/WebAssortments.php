<?php

namespace Modules\Erp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Entities\ErpImport;
use Modules\Erp\Traits\HasErpMapper;

class WebAssortments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpMapper;

    public $skip_token;

    public function __construct(?string $skip_token = null)
    {
        $this->skip_token = $skip_token;
    }

    public function handle(): void
    {
        $this->erpImport("webAssortments", $this->url."webAssortments", $this->skip_token);
    }
}
