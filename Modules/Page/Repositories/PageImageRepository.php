<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\PageImage;
use Exception;

class PageImageRepository extends BaseRepository
{
    public function __construct(PageImage $pageImage)
    {
        $this->model = $pageImage;
        $this->model_key = "page.images";
        $this->rules = [
            "page_id" => "required|exists:pages,id",
            "image.*" => "required|mimes:bmp,jpeg,jpg,png",
        ];
    }

    public function createImage($file): array
    {
        DB::beginTransaction();

        try
        {
            $key = \Str::random(6);
            $file_name = $file->getClientOriginalName();
            $path = "images/pages/{$key}";
            $data['path'] = $file->storeAs($path, $file_name);

            if(!Storage::has($path)) Storage::makeDirectory($path, 0777, true, true);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $data;
    }
}