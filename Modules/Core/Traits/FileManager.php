<?php

namespace Modules\Core\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FileManager
{


    protected function uploadFile(UploadedFile $file, $upload_path)
    {
        //Saving a file with original for better seo
        $filenameWithExt = $file->getClientOriginalName();

        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // Get just extension
        $extension = $file->getClientOriginalExtension();

        //Filename to store
        $fileNameToStore = $filename . '_' . time() . Str::random(5) . '.' . $extension;

        //check and createFolder if not exist
        $this->createFolderIfNotExist($upload_path);

        // Upload Image
        $file->move($upload_path, $fileNameToStore);
        return $fileNameToStore;
    }

    private function createFolderIfNotExist($path)
    {
        if (!file_exists($path)) {
            File::makeDirectory($path, $mode = 0755, true, true);
        }
    }

    protected function removeFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }


}