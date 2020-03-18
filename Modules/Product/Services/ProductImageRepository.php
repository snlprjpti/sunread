<?php

namespace Modules\Product\Services;

use Modules\Core\Traits\FileManager;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductImage;

class ProductImageRepository
{
    use FileManager;
    protected  $folder_path,$product_image_path;
    private $folder = 'product';
    public function __construct()
    {
        $this->folder_path = storage_path('app/public/');
        $this->product_image_path = 'images/product/';
    }
    public function uploadProductImages($product)
    {
        try {
            $productImageIds = [];
            $this->createFolderIfNotExist($this->folder_path);
            if (request()->hasFile('images')) {
                $files = request()->file('images');
                foreach ($files as $file) {
                    $fileName = $this->getFileName($file);
                    $file->move($this->folder_path.$this->product_image_path, $fileName);
                    $image_type_array = $this->getImageType($product);
                    $productImage = ProductImage::create(array_merge([
                            'product_id' => $product->id,
                            'path' => $this->product_image_path.$fileName
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
                if (file_exists($this->folder_path . $productImage->path)) {
                    unlink(($this->folder_path . $productImage->path));
                }
                $productImage->delete();
            }
            return true;
        }

        return false;
    }

    public function removeParticularProductImage($productImage):bool
    {
        if ($productImage && isset($productImage->path) && $productImage->path != '') {
            if (file_exists($this->folder_path . $productImage->path)) {
                unlink(($this->folder_path . $productImage->path));
            $productImage->delete();
            return true;
            }
        }
        return false;
    }

    private function getImageType(Product $product):array
    {
        $image_type_array= [];
        $productImagesCount = ProductImage::where('product_id',$product->id)->count();
        if($productImagesCount == 0) {
            return $image_type_array = [
                'main_image' => 1,
                'small_image' => 1,
                'thumbnail' => 1,
            ];

        }
        return $image_type_array;
    }
}
