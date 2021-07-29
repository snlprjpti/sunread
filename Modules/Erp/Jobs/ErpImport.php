<?php

namespace Modules\Erp\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Modules\Erp\Entities\ErpImport as EntitiesErpImport;
use Modules\Erp\Entities\ErpImportDetail;
use Modules\Erp\Jobs\ErpProductDescription;

class ErpImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;

    public function __construct( string $type)
    {
        $this->type = $type;
    }

    public function handle(): void
    {
        try
        {
            $erp_import_id = EntitiesErpImport::whereType($this->type)->first()->id;
            
			cache()->get($this->type)->each(function ($item) use ($erp_import_id) {
                
                if ( array_key_exists("no", $item) )
                {
                    $sku = $item["no"];
                }
                elseif (array_key_exists("Item_No", $item))
                {
                    $sku = $item["Item_No"];
                }
                else{
                    $sku = $item["itemNo"];
                }

                ErpImportDetail::updateOrInsert([
					"erp_import_id" => $erp_import_id,
					"sku" => $sku,
					"value" => json_encode($item)
				]);

                if ( $this->type == "listProducts" ) ErpProductDescription::dispatchSync($sku);
			});
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
