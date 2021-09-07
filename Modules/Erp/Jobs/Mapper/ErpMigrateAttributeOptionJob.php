<?php

namespace Modules\Erp\Jobs\Mapper;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Entities\ErpImport;
use Modules\Erp\Traits\HasErpValueMapper;

class ErpMigrateAttributeOptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;

    public function __construct()
    {

    }

    public function handle()
    {
        try
        {
            $erp_details = ErpImport::where("type", "listProducts")->first()->erp_import_details;

            $chunked = $erp_details->chunk(100); 
            foreach ( $chunked as $chunk )
            {
                foreach ( $chunk as $detail )
                {
                    if ( $detail->value["webAssortmentWeb_Active"] == false ) continue;
                    if ( $detail->value["webAssortmentWeb_Setup"] != "SR" ) continue;
                    $this->createOption($detail);
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
