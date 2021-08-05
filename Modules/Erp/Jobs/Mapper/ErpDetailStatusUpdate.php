<?php

namespace Modules\Erp\Jobs\Mapper;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Entities\ErpImportDetail;

class ErpDetailStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(): void
    {
        try
        {
            ErpImportDetail::whereId($this->id)->update(["status" => 1]);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
