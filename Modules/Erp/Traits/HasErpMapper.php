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
use Modules\Erp\Jobs\ImportErpData;

trait HasErpMapper
{
    protected $url = "https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/";
    public $erp_folder = "ERP Product Images";

    private function basicAuth(): object
    {
        return Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"));
    }

    public function erpImport( string $type, string $url, ?string $skip_token = null ): Collection
    {
        try
        {
            $response_json = $this->getResponse($type, $url, $skip_token, function ($response_json_array, $new_skip_token) use ($type) {
                ImportErpData::dispatch($type, $response_json_array);
                get_class()::dispatch($new_skip_token);
            });

            // return $this->generateCollection($response_json, $type);

            // Cache::forget($type);
            // Cache::rememberForever($type, function () use ($response_json, $type) {
            //     // get all values fron erp api
            //     return $this->generateCollection($response_json, $type);
            // });

            // JobsErpImport::dispatch($type);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $this->generateCollection($response_json, $type);
    }

    public function getResponse(string $type, string $url, ?string $skip_token = null, callable $callback = null): array
    {
        try
        {
            $response = $this->basicAuth()->get($url, $skip_token);

            $response_json_array = $response->json()["value"];
            $skip_token = $this->skipToken(end($response_json_array), $type);

            if (!empty($response_json_array) && array_key_exists("@odata.nextLink", $response->json())) {
                if ( $callback ) $callback($response_json_array, $skip_token);
            } else {
                // ErpImport::whereType($type)->update(["status" => 1]);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $response_json_array;
    }

    private function skipToken( array $data, string $type ): string
    {
        $data = (object) $data;
        $prepend = "\$skiptoken";

        switch ($type) {
            case 'webAssortments':
                $token = "{$prepend}='{$data->itemNo}',".'SR'.",'{$data->colorCode}'";
                break;
            
            case 'listProducts':
                $token = "{$prepend}='{$data->no}',"."'{$data->webAssortmentWeb_Setup}',"."'{$data->webAssortmentColor_Code}'".",'{$data->languageCode}'".",'{$data->auxiliaryIndex1}'".",'{$data->auxiliaryIndex2}'".",'{$data->auxiliaryIndex3}'".",'{$data->auxiliaryIndex4}'";
                break;
            
            case 'attributeGroups':
                $token = "{$prepend}='{$data->itemNo}',"."'{$data->sortKey}',"."'{$data->groupCode}',"."'{$data->attributeID}',"."'{$data->name}',"."'{$data->auxiliaryIndex1}'";
                break;
            
            case 'productVariants':
                $token = "{$prepend}='{$data->pfVerticalComponentCode}','{$data->itemNo}'";
                break;

            case 'salePrices':
                $token = '$skiptoken'."'{$data->itemNo}',"."'{$data->salesCode}',"."'{$data->currencyCode}',"."'{$data->startingDate}',"."'{$data->salesType}',"."'{$data->minimumQuantity}',"."'{$data->unitofMeasureCode}',"."'{$data->variantCode}'";
                break;

            case 'eanCodes':
                $token = '$skiptoken'."'{$data->itemNo}',"."'{$data->variantCode}',"."'{$data->unitofMeasure}',"."'{$data->crossReferenceType}',"."'{$data->crossReferenceTypeNo}',"."'{$data->crossReferenceNo}'";
                break;

            case 'webInventories':
                $token = '$skiptoken'."'{$data->Item_No}',"."'{$data->Code}'";
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

                if ( !$file_does_not_already_exist ) {
                    $remote_hash = md5(Storage::disk("ftp")->size($file));
                    $local_hash = md5(Storage::size("{$this->erp_folder}/{$file}"));

                    $file_does_not_already_exist = $remote_hash !== $local_hash;
                }

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
            $this->storeFileToDb($location);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    public function storeFileToDb(string $location): void
    {
        try
        {
            $erp_import_id = 9;
            $file_arr = explode("_", explode(".", explode("/", $location)[1])[0]);
            $file_info = [
                "sku" => $file_arr[0],
                "color_code" => $file_arr[1],
                "image_type" => $file_arr[2],
                "url" => "{$this->erp_folder}/{$location}"
            ];
            $hash = md5($erp_import_id.$file_info["sku"].json_encode($file_info));

            ErpImportDetail::create([
                "erp_import_id" => $erp_import_id,
                "sku" => $file_info["sku"],
                "value" => json_encode($file_info),
                "hash" => $hash
            ]);
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
                $value = json_encode(["description" => $response->body(), "lang" => "ENU"]);

                ErpImportDetail::updateOrInsert([
                    "erp_import_id" => $erp_import_id,
                    "sku" => $sku,
                    "value" => json_encode($value)
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
