<?php

namespace Modules\Product\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductImage;

class MapProductImageTypeValueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(): void
    {
        try 
        {
            $product_images = ProductImage::whereProductId($this->id)->get();

            foreach ( $product_images as $product_image )
            {
                if ( $product_image->gallery ) continue;
                $this->getSimilarImages($product_image, $product_images);
            }
        } 
        catch (Exception $exception) 
        {
            throw $exception;
        }
    }

    private function getSimilarImages(object $image, mixed $product_images): mixed
    {
        $name = explode("/",$image->path);
        $ids = [];
        return $product_images->filter(fn ($img) => $image->id !== $img->id)->map(function ($product_image) use ($name, $image, &$ids) {
            $other_name = explode("/", $product_image->path);            
            $types = (end($name) === end($other_name)) ? $this->getImageType($product_image) : [];
            $image->update($types);
            return (end($name) === end($other_name)) ? $ids[] = $product_image->id : "";
        })->toArray();
    }

    private function getImageType(object $type): ?array
    {
        $types = [];
        if ( $type->main_image ) $types = array_merge($types, [ "main_image" => $type->main_image ]);
        if ( $type->small_image ) $types = array_merge($types, [ "small_image" => $type->small_image ]);
        if ( $type->thumbnail ) $types = array_merge($types, [ "thumbnail" => $type->thumbnail ]);
        if ( $type->section_background ) $types = array_merge($types, [ "section_background" => $type->section_background ]);
        return $types;
    }
}
