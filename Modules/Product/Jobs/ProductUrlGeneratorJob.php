<?php

namespace Modules\Product\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Website;
use Illuminate\Support\Str;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductAttributeString;

class ProductUrlGeneratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
       try
       {
           $products = Product::with(["product_attributes"])->get();
           foreach ($products as $product)
           {
                $product_name = $product->value([
                    "scope" => "website",
                    "scope_id" => $product->id,
                    "attribute_slug" => "name"
                ]);
                $product_attribute = ProductAttribute::whereProductId($product->id)
                ->whereAttributeId(18)->whereScope("website")
                ->whereScopeId($product->website_id)->first();

                $product_attribute->value()->each(function($attribute_value) use($product_name) {
                    $attribute_value->update(["value" => $this->createSlug($product_name)]);
                });
           }
       }
       catch ( Exception $exception )
       {
           throw $exception;
       }
    }

    public function createSlug(string $title, int $id = 0): string
    {
       try
       {
            // Slugify
            $slug = Str::slug($title);
            $original_slug = $slug;

            // Throw Error if slug could not be generated
            if ($slug == "") throw new SlugCouldNotBeGenerated();

            // Get any that could possibly be related.
            // This cuts the queries down by doing it once.
            $allSlugs = $this->getRelatedSlugs($slug, $id);

            // If we haven't used it before then we are all good.
            if (!$allSlugs->contains('value', $slug)) return $slug;

            //if used,then count them
            $count = $allSlugs->count();

            // Loop through generated slugs
            while ($this->checkIfSlugExist($slug, $id) && $slug != "") {
                $slug = "{$original_slug}-{$count}";
                $count++;
            }
       }
       catch ( Exception $exception )
       {
           throw $exception;
       }

        // Finally return Slug
        return $slug;
    }

    private function getRelatedSlugs(string $slug, int $id = 0): object
    {
        return ProductAttributeString::whereRaw("value RLIKE '^{$slug}(-[0-9]+)?$'")
            ->where('id', '<>', $id)
            ->get();
    }

    private function checkIfSlugExist(string $slug, int $id = 0): ?bool
    {
        return ProductAttributeString::select('value')->where('value', $slug)
            ->where('id', '<>', $id)
            ->exists();
    }
}
