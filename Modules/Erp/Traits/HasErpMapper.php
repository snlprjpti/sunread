<?php

namespace Modules\Erp\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Modules\Erp\Entities\ErpImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Erp\Entities\ErpImportDetail;
use Modules\Erp\Jobs\ErpImport as JobsErpImport;
use Modules\Erp\Jobs\FtpToStorage;

trait HasErpMapper
{
    protected $url = "https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/";
    public $erp_folder = "ERP Product Images";

    private function basicAuth(): object
    {
        return Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"));
    }

    public function erpImport( string $type, string $url ): Collection
    {
        try
        {
            // Get ERP API
            $response = $this->basicAuth()->get($url);
            
            // values refers to response values
            $values = [];
            $values = $response->json()["value"];

            // last refers to response last values
            $last = [];
            $last[] = end($values);
            
            while ( true )
            {
                // Generate skip token
                $last_value = end($last);
                $skip = $this->skipToken($last_value, $type);

                // Get current page values
                $paginated = $this->basicAuth()->get($url, $skip);
                
                // End iteration if current value is empty 
                if (empty($paginated->json()["value"]) || !array_key_exists("@odata.nextLink", $paginated->json())) break;
    
                // Merge last value for generating skip token for next page.
                $last[] = end($paginated->json()["value"]);

                // Merge all values 
                $values = array_merge($values, $paginated->json()["value"]);
            }

            Cache::forget($type);
            Cache::rememberForever($type, function () use ($values, $type) {
                // get all values fron erp api
                return $this->generateCollection($values, $type);
            });

            JobsErpImport::dispatch($type);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $this->generateCollection($values, $type);
    }

    private function skipToken( array $data, string $type ): string
    {
        switch ($type) {
            case 'webAssortments':
                $token = '$skiptoken='."'{$data['itemNo']}',".'SR'.",'{$data['colorCode']}'";
                break;
            
            case 'listProducts':
                $token = '$skiptoken='."'{$data['no']}',"."'{$data['webAssortmentWeb_Setup']}',"."'{$data['webAssortmentColor_Code']}'".",'{$data['languageCode']}'".",'{$data['auxiliaryIndex1']}'".",'{$data['auxiliaryIndex2']}'".",'{$data['auxiliaryIndex3']}'".",'{$data['auxiliaryIndex4']}'";
                break;
            
            case 'attributeGroups':
                $token = '$skiptoken='."'{$data['itemNo']}',"."'{$data['sortKey']}',"."'{$data['groupCode']}',"."'{$data['attributeID']}',"."'{$data['name']}',"."'{$data['auxiliaryIndex1']}'";
                break;
            
            case 'productVariants':
                $token = '$skiptoken='."'{$data['pfVerticalComponentCode']}',"."'{$data['itemNo']}'";
                break;

            case 'salePrices':
                $token = '$skiptoken'."'{$data['itemNo']}',"."'{$data['salesCode']}',"."'{$data['currencyCode']}',"."'{$data['startingDate']}',"."'{$data['salesType']}',"."'{$data['minimumQuantity']}',"."'{$data['unitofMeasureCode']}',"."'{$data['variantCode']}'";
                break;

            case 'eanCodes':
                $token = '$skiptoken'."'{$data['itemNo']}',"."'{$data['variantCode']}',"."'{$data['unitofMeasure']}',"."'{$data['crossReferenceType']}',"."'{$data['crossReferenceTypeNo']}',"."'{$data['crossReferenceNo']}'";
                break;

            case 'webInventories':
                $token = '$skiptoken'."'{$data['Item_No']}',"."'{$data['Code']}'";
        }

        return $token;
    }

    private function generateCollection( array $data, string $type ): Collection
    {
        switch ($type) {
            case 'listProducts':
                $collection = collect($data)->where("webAssortmentWeb_Active", true)
                    ->where("webAssortmentWeb_Setup", "SR")
                    ->chunk(50)
                    ->flatten(1);
                break;

            case 'webAssortments':
                $collection = collect($data)->where("webActive", true)
                    ->where("webSetup", "SR")
                    ->chunk(50)
                    ->flatten(1);
                break;

            default :
                $collection = collect($data)
                    ->chunk(50)
                    ->flatten(1);
                break;
        }

        return $collection;
    }

    public function storeImage(): bool
    {
        try
        {
            $ftp_directories = Storage::disk("ftp")->directories();
            $ftp_files = Storage::disk("ftp")->files("/{$ftp_directories[0]}");

            $ftp_files = array_filter($ftp_files, function ($file) {
                $file_is_image = Str::contains($file, [".jpg", ".jpeg", ".png", ".bmp"]);
                $file_does_not_already_exist = !Storage::exists("{$this->erp_folder}/{$file}");

                return $file_is_image && $file_does_not_already_exist;
            });

            foreach ( $ftp_files as $file )
            {
                FtpToStorage::dispatch($file);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

    public function storeFtpToLocal(string $location): void
    {
        try
        {
            $get_file = Storage::disk("ftp")->get($location);
            Storage::put("{$this->erp_folder}/{$location}", $get_file);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    public function storeDescription(string $sku): bool
    {
        try
        {
            $erp_import_id = ErpImport::whereType("productDescriptions")->first()->id;

            $url = "{$this->url}webExtendedTexts(tableName='Item',No='{$sku}',Language_Code='ENU',textNo=1)/Data";
            $response = $this->basicAuth()->get($url);
            if ( $response->status() == 200 )
            {
                $value = json_encode(["description" => $response->body()]);

                ErpImportDetail::updateOrInsert([
                    "erp_import_id" => $erp_import_id,
                    "sku" => $sku,
                    "value" => json_encode($value),
                    "created_at" => now(),
                    "updated_at" => now()
                ]);

            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }
}
