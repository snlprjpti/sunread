<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [];

    public function __construct()
    {

    }

    public function uploadMainImage($request)
    {

        // resizing image: 100 * 100 | 500 * 500
        foreach ($this->main_image_dimensions as $dimension) {

            $img = Image::make($this->folder_path . DIRECTORY_SEPARATOR . $file_name)->resize($dimension['width'], $dimension['height']);
            $img->save($this->folder_path . DIRECTORY_SEPARATOR . $dimension['width'] . '_' . $dimension['height'] . '_' . $file_name);

        }
        $request->request->add([
                'main_image' => $file_name
            ]);

    }

    public function galleryImages($request)
    {


        // uploading image gallery
        if ($request->hasFile('gallery_image')) {

            foreach ($request->file('gallery_image') as $key => $item) {

                $file = $item;
                $file_name = rand(4100, 9998) . '_' . $file->getClientOriginalName();

                $file->move($this->folder_path, $file_name);

                // resizing image
                foreach ($this->gallery_image_dimensions as $dimension) {
                    $img = Image::make($this->folder_path . DIRECTORY_SEPARATOR . $file_name)->resize($dimension['width'], $dimension['height']);
                    $img->save($this->folder_path . DIRECTORY_SEPARATOR . $dimension['width'] . '_' . $dimension['height'] . '_' . $file_name);
                    
                }

                // store content in db
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $file_name,
                    'alt_text' => $request->get('gallery_image_alt_text')[$key],
                    'caption' => $request->get('gallery_image_caption')[$key],
                    'status' => $request->get('gallery_image_status')[$key],
                ]);

            }

        }



    }
    
    
}
