<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\ProductImage;

class ProductImageRepository extends BaseRepository
{
    public function __construct(ProductImage $productImage)
    {
        $this->model = $productImage;
        $this->model_key = "catalog.product.images";
        $this->rules = [
            "product_id" => "required|exists:products,id",
            "position" => "sometimes|numeric",
            "main_image" => "sometimes|boolean",
            "small_image" => "sometimes|boolean",
            "thumbnail" => "sometimes|boolean"
        ];
    }


    public function removeImage(int $id): object
    {
        try
        {
            $updated = $this->model->findOrFail($id);
            if (!$updated->path) {
                return $updated;
            }
            $path_array = explode("/", $updated->path);
            unset($path_array[count($path_array) - 1]);
            $delete_folder = implode("/", $path_array);
            rmdir($delete_folder);
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        return $updated;
    }
}
