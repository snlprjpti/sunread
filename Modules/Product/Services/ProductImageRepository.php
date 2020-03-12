<?php

namespace Modules\Product\Services;

use Intervention\Image\Facades\Image;
use Modules\Core\Traits\FileManager;
use Modules\Product\Entities\ProductImage;

class ProductImageRepository
{
    use FileManager;
    protected $main_image_dimensions, $gallery_image_dimensions, $folder_path;
    private $folder = 'product';

    public function __construct()
    {
        $this->main_image_dimensions = config('sunread.image_dimensions.product.main_image');
        $this->folder_path = storage_path('app/public/images/') . $this->folder . DIRECTORY_SEPARATOR;
    }

    public function uploadProductImages($data, $product)
    {
        $this->uploadProductMainImage($product);
        $this->uploadProductGalleryImages($product);
    }

    public function uploadProductMainImage($product)
    {
        try {

            $file = request()->file('main_image');
            if (!$file) {
                return;
            }
            $fileName = $this->getFileName($file);
            $this->createFolderIfNotExist($this->folder_path);

            //Store main image
            $file->move($this->folder_path, $fileName);
            $main_images = array([
                'product_id' => $product->id,
                'type' => 'main_image',
                'image' => $fileName
            ]);

            //Store small and thumbnail image
            foreach ($this->main_image_dimensions as $key => $dimension) {

                //resolution and size can be changed with object chaining
                $img = Image::make($this->folder_path . DIRECTORY_SEPARATOR . $fileName)
                    ->resize($dimension['width'], $dimension['height']);
                $updated_file_name = $key . '_' . $fileName;
                $img->save($this->folder_path . DIRECTORY_SEPARATOR . $updated_file_name);

                $main_images[] = [
                    'product_id' => $product->id,
                    'type' => $key,
                    'image' => $updated_file_name
                ];
            }

            //Remove old main,small and thumbnail images
            $previousMainImages = $product->images()->where('type', '!=', 'gallery_image')->get();
            foreach ($previousMainImages as $previousMainImage) {
                if (file_exists($this->folder_path . $previousMainImage->image))
                    unlink($this->folder_path . $previousMainImage->image);
                $previousMainImage->delete();
            }

            //Then Bulk Insert query to save connection time
            ProductImage::insert($main_images);

        } catch (\Exception $exception) {
            throw  $exception;
        }

    }

    public function uploadProductGalleryImages($product)
    {

        try {
            $gallery_images = [];
            $this->createFolderIfNotExist($this->folder_path);
            if ($files = request()->hasFile('gallery_images')) {
                foreach ($files as $file) {
                    $fileName = $this->getFileName($file);
                    $file->move($this->folder_path, $fileName);
                    $gallery_images[] = [
                        'product_id' => $product->id,
                        'type' => 'gallery_image',
                        'image' => $fileName
                    ];

                }

            }

            //Bulk insertion
            ProductImage::insert($gallery_images);
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