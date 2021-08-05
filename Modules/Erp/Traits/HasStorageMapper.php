<?php

namespace Modules\Erp\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Erp\Entities\ErpImportDetail;
use Modules\Erp\Jobs\FtpToStorage;

trait HasStorageMapper
{
    public $erp_folder = "ERP-Product-Images";

    public function storeFromFtpImage(): void
    {
        try
        {
            $ftp_directories = Storage::disk("ftp")->directories();
            $ftp_files = Storage::disk("ftp")->files("/{$ftp_directories[0]}");

            // Filter files that are image and not already in local storage
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
    }

    public function storeFromLocalImage(): void
    {
        try
        {
            // Filter Images only
            $files = array_filter(Storage::files("/{$this->erp_folder}/COLECT.IO"), fn ($file) => Str::contains($file, [".jpg", ".jpeg", ".png", ".bmp"]));

            $hases = [];
            $files = array_map(function ($file) use (&$hases) {
                $file_data = $this->generateFileData($file);
                $hases[] = $file_data["hash"];

                return $file_data;
            }, $files);
            
            // Filter non-existing Images only
            $existing_hashes = ErpImportDetail::whereIn("hash", $hases)->get()->pluck("hash")->toArray();
            $files = array_filter($files, fn ($file) => !in_array($file["hash"], $existing_hashes));

            ErpImportDetail::insert($files);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    public function transferFtpToLocal(string $location): void
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
            $file_data = $this->generateFileData($location);
            ErpImportDetail::insert($file_data);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function generateFileData(string $file): array
    {
        try
        {
            $erp_import_id = 9;
            $file_arr = explode("_", explode(".", array_reverse(explode("/", $file))[0])[0]);
            $file_info = [
                "sku" => $file_arr[0],
                "color_code" => $file_arr[1],
                "image_type" => $file_arr[2],
                "url" => "{$this->erp_folder}/COLECT.IO/{$file}"
            ];
            $hash = md5($erp_import_id.$file_info["sku"].json_encode($file_info));
            
            $file_data = [
                "erp_import_id" => $erp_import_id,
                "sku" => $file_info["sku"],
                "value" => $file_info,
                "hash" => $hash,
                "created_at" => now(),
                "updated_at" => now()
            ];
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $file_data;
    }
}
