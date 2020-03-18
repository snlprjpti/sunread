<?php

namespace Modules\Product\Services;

use Modules\Core\Traits\FileManager;
use Modules\Product\Entities\ProductImage;

class ProductImageRepository
{
    use FileManager;
    protected  $folder_path;
    private $folder = 'product';

    public function __construct()
    {
        $this->folder_path = storage_path('app/public/images/') . $this->folder . DIRECTORY_SEPARATOR;
    }



    public function uploadProductImages($product)
    {
        try {
            $productImageIds = [];
            $this->createFolderIfNotExist($this->folder_path);
            $firstImageExists = false;
            $productImages = $product->images;
            if(isset($productImages) && $productImages->count()>0){
                $firstImageExists = true;
            }
            $image_type_array = [];
            if (request()->hasFile('images')) {
                $files = request()->file('images');
                foreach ($files as $file) {
                    $fileName = $this->getFileName($file);
                    $path = 'images/'.$this->folder.$fileName;
                    $file->move($this->folder_path, $fileName);
                    if(!$firstImageExists){
                         $image_type_array = [
                            'main_image' => 1,
                            'small_image' => 1,
                            'thumbnail' => 1,
                        ];
                    }
                    $productImage = ProductImage::create(array_merge([
                            'product_id' => $product->id,
                            'path' => $path
                    ],$image_type_array));
                    $productImageIds[] = $productImage->id;
                }

            }

            return $productImageIds;
        } catch (\Exception $exception) {
            throw  $exception;
        }

    }

    public function removeProductImages($product)
    {
        $productImages = $product->images;
        if(isset($productImages) && $productImages->count()>0){
            foreach ($productImages as $productImage){
                if (file_exists($this->folder_path . $productImage->image)) {
                    unlink(($this->folder_path . $productImage->image));
                }
                $productImage->delete();
            }
            return true;
        }

        return false;
    }

    public function removeParticularProductImage($productImage)
    {
        if ($productImage) {
            if (file_exists($this->folder_path . $productImage->image)) {
                unlink(($this->folder_path . $productImage->image));
            $productImage->delete();
            return true;
            }
        }
        return false;
    }

}
