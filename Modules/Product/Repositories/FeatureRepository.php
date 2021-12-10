<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\Feature;
use Exception;

class FeatureRepository extends BaseRepository
{
    public function __construct(Feature $feature)
    {
        $this->model = $feature;
        $this->model_key = "catalog.features";
        $this->rules = [
            "name" => "required",
            "description" => "nullable",
            "status" => "sometimes|boolean",
            // translation validation
            "translations" => "nullable|array",
        ];
    }

    public function createImage(object $request): ?string
    {
        try
        {
            if(empty($request->image)) return null;
            $request->validate([
                'image' => 'mimes:jpeg,jpg,png',
            ]);

            /** get image size */
            $dimension = config('product_image.image_dimensions.feature_image');

            $file = $request->file("image");
            $key = Str::random(6);
            $file_name = $this->generateFileName($file);
            $data = $file->storeAs("images/features/{$key}", $file_name);

            // Store image
            $width = $dimension[0]["width"];
            $height = $dimension[0]["height"];

            $path = "images/features/{$key}";
            if(!Storage::has($path)) Storage::makeDirectory($path, 0777, true, true);

            $image = Image::make($file)
                ->fit($width, $height, function($constraint) {
                    $constraint->upsize();
                })
                ->encode('jpg', 80);
            Storage::put("$path/{$file_name}", $image) ? 1 : 0;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }


    public function removeImage(object $deleted): bool
    {
        try
        {
            if (!$deleted->image) {
                return true;
            }

            $path_array = explode("/", $deleted->image);
            unset($path_array[count($path_array) - 1]);

            $delete_folder = implode("/", $path_array);
            Storage::disk("public")->deleteDirectory($delete_folder);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }
}
