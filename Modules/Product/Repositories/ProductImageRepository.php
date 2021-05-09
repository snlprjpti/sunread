<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\ProductImage;
use Intervention\Image\Facades\Image;
use Exception;

class ProductImageRepository extends BaseRepository
{
    public function __construct(ProductImage $productImage)
    {
        $this->model = $productImage;
        $this->model_key = "catalog.product.images";
        $this->small_image_dimensions = config('product_image.image_dimensions.product_small_image');
        $this->thumbnail_image_dimensions = config('product_image.image_dimensions.product_thumbnail_image');
        $this->rules = [
            "product_id" => "required|exists:products,id",
            "image" => "required|mimes:bmp,jpeg,jpg,png",
            "position" => "sometimes|numeric",
            "main_image" => "sometimes|boolean"
        ];
    }

    public function createImage($request): array
    {
        DB::beginTransaction();

        try
        {

            // Store File
            $file = $request->file("image");
            $key = \Str::random(6);
            $file_name = $file->getClientOriginalName();
            $data['path'] = $file->storeAs("images/products/{$key}", $file_name, ["disk" => "public"]);


            // Store main_image and gallery_image variations
            foreach (["small_image_dimensions" => "small_image", "thumbnail_image_dimensions" => "thumbnail"] as $type => $folder) {
                foreach ($this->{$type} as $dimension) {
                    $width = $dimension["width"];
                    $height = $dimension["height"];
                    $path = "images/products/{$key}/{$folder}";
                    if(!Storage::has($path)) Storage::makeDirectory($path, 0777, true, true);

                    $image = Image::make($file)
                        ->fit($width, $height, function($constraint) {
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80);
                    $data[$folder] = Storage::put("$path/{$file_name}", $image)?1:0;
                }
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $data;
    }


    public function deleteThumbnail(int $id): object
    {
        DB::beginTransaction();

        try
        {
            $updated = $this->model->findOrFail($id);

            $path_array = explode("/", $updated->path);
            unset($path_array[count($path_array) - 1]);

            $delete_folder = implode("/", $path_array);
            $file = substr($updated->path, strrpos($updated->path, '/' )+1);
            foreach ([ "small_image","thumbnail"] as $subfolder) {
                $folder = $delete_folder.'/'.$subfolder.'/'.$file;
                Storage::disk("public")->delete($folder);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return $updated;
    }
}
