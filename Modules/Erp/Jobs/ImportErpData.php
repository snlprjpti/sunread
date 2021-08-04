<?php

namespace Modules\Erp\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Modules\Erp\Entities\ErpImport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Erp\Entities\ErpImportDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportErpData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $erp_data, $type;

    public function __construct(string $type, array $erp_data)
    {
        $this->type = $type;
        $this->erp_data = $erp_data;
    }

    public function handle(): void
    {
        try
        {
            $erp_import_id = ErpImport::whereType($this->type)->first()->id;
            if ( !$erp_import_id) throw new Exception("Invalid Type");

            $chunked = array_chunk($this->erp_data, 100);

            foreach ($chunked as $chunk) {
                $hashes = [];

                $import_data = array_map(function ($item) use ($erp_import_id, &$hashes) {
                    if ( array_key_exists("no", $item) ) {
                        $sku = $item["no"];
                    } elseif (array_key_exists("Item_No", $item)) {
                        $sku = $item["Item_No"];
                    } else {
                        $sku = $item["itemNo"];
                    }

                    if ( $this->type == "listProducts" ) ErpProductDescription::dispatch($sku);

                    $item = json_encode($item);
                    $item_hash = md5($erp_import_id.$sku.$item);
                    $hashes[] = $item_hash;
                    return [
                        "erp_import_id" => $erp_import_id,
                        "sku" => $sku,
                        "value" => $item,
                        "hash" => $item_hash,
                        "created_at" => now(),
                        "updated_at" => now()
                    ];
                }, $chunk);

                $existing_details = ErpImportDetail::whereIn("hash", $hashes)->get()->pluck("hash")->toArray();
                $import_data = array_filter($import_data, function ($item) use ($existing_details) {
                    return !in_array($item["hash"], $existing_details);
                });

                ErpImportDetail::insert($import_data);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
