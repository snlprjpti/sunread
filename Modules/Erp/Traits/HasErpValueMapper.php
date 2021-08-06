<?php

namespace Modules\Erp\Traits;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Modules\Erp\Entities\ErpImport;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\ProductImage;
use Modules\Product\Entities\ProductAttribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Erp\Entities\ErpImportDetail;
use Modules\Erp\Jobs\Mapper\ErpDetailStatusUpdate;
use Modules\Erp\Jobs\Mapper\ErpGenerateVariantProductJob;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductImageJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductAttributeJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductInventoryJob;

trait HasErpValueMapper
{
    protected array $erp_types = [
        "webAssortments",
        "listProducts",
        "attributeGroups",
        "salePrices",
        "eanCodes",
        "webInventories",
        "productDescriptions",
        "productVariants",
        "productImages"
    ];

    protected array $erp_attributes = [
        "Features",
        "Size and Care",
        "Ean Code"
    ];

    public function importAll(): void
    {
        try
        {
            $erp_details = ErpImport::where("type", "listProducts")->first()->erp_import_details;

            $chunked = $erp_details->chunk(100); 
            foreach ( $chunked as $chunk )
            {
                foreach ( $chunk as $detail )
                {
                    if ( $detail->status == 1 ) continue;

                    if ( !$detail->value["webAssortmentWeb_Active"] == true && !$detail->value["webAssortmentWeb_Setup"] == "SR" ) continue;

                    $check_variants = ($this->getDetailCollection("productVariants", $detail->sku)->count() > 1);
                    $type = ($check_variants) ? "configurable" : "simple";

                    $match = [
                        "website_id" => 1,
                        "sku" => $detail->sku
                    ];
                    $product_data = array_merge($match, [
                        "attribute_set_id" => 1,
                        "type" => $type,
                    ]);
        
                    $product = Product::updateOrCreate($match, $product_data);
                    ErpMigrateProductImageJob::dispatch($product, $detail);

                    if ($check_variants) ErpGenerateVariantProductJob::dispatch($product, $detail);

                    //visibility attribute value
                    $visibility = ($check_variants) ? 5 : 8;
                    
                    ErpMigrateProductAttributeJob::dispatch($product, $detail, false, $visibility);
                    ErpMigrateProductInventoryJob::dispatch($product, $detail);
                    ErpDetailStatusUpdate::dispatch($detail->id);
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function createAttributeValue(object $product, object $erp_product_iteration, bool $ean_code_value = true, int $visibility = 8, mixed $variant = null): void
    {
        try
        {
            $this->getAttributeOptionValue($erp_product_iteration, "color");
            $ean_code = $this->getDetailCollection("eanCodes", $erp_product_iteration->sku);
            $variants = $this->getDetailCollection("productVariants", $erp_product_iteration->sku);

            if ( $ean_code_value ) {
                $pluck_code = $this->getValue($variants)->first()["code"];
                $ean_code_value = $this->getValue($ean_code)->where("variantCode", $pluck_code)->first()["crossReferenceNo"] ?? "";
            } else {
                $ean_code_value = $this->getValue($ean_code)->first()["crossReferenceNo"] ?? "";
            }

            $description_value = $this->getDetailCollection("productDescriptions", $erp_product_iteration->sku);
            $description = ($description_value->count() > 1) ? json_decode($this->getValue($description_value)->first(), true)["description"] ?? "" : "";
            
            // get price for specific product need more clearification
            $price = $this->getDetailCollection("salePrices", $erp_product_iteration->sku);
            $price_value = ($price->count() > 1) ? $this->getValue($price)->where("currencyCode", "USD")->first() ?? ["unitPrice" => 0.0] : ["unitPrice" => 0.0];

            // Condition for invalid date/times
            $max_time = strtotime("2030-12-28");
            $start_time = abs(strtotime($price_value["startingDate"]));
            $end_time = abs(strtotime($price_value["endingDate"]));

            $start_time = $start_time < $max_time ? $start_time : $max_time - 1;
            $end_time = $end_time < $max_time ? $end_time : $max_time;

            $attribute_data = [
                [
                    "attribute_id" => 1,
                    "value" => $erp_product_iteration->value["description"]
                ],
                [
                    "attribute_id" => 3,
                    "value" => $price_value["unitPrice"], 
                ],
                [
                    "attribute_id" => 6,
                    "value" => Carbon::parse(date("Y-m-d", $start_time)), 
                ],
                [
                    "attribute_id" => 7,
                    "value" => Carbon::parse(date("Y-m-d", $end_time)), 
                ],
                [
                    "attribute_id" => 11,
                    "value" => $visibility, 
                ],
                [
                    "attribute_id" => 16,
                    "value" => $description, 
                ],
                [
                    "attribute_id" => 17,
                    "value" => Str::limit($description, 100), 
                ],
                [
                    "attribute_id" => 19,
                    "value" => $description, 
                ],
                [
                    "attribute_id" => 20,
                    "value" => $erp_product_iteration->value["description"], 
                ],
                [
                    "attribute_id" => 21,
                    "value" => $description, 
                ],
                [
                    "attribute_id" => 22,
                    "value" => 1, 
                ],
                [
                    "attribute_id" => 26,
                    "value" => $this->getAttributeOptionValue($erp_product_iteration, "color"), 
                ],
                [
                    "attribute_id" => 27,
                    "value" => ($this->getDetailCollection("productVariants", $erp_product_iteration->sku)->count() > 1) ? $this->getAttributeOptionValue($variant, "size") : "", 
                ],
                [
                    "attribute_id" => 28,
                    "value" => $this->getAttributeValue($product, $erp_product_iteration ,"Features" ), 
                ],
                [
                    "attribute_id" => 29,
                    "value" => $this->getAttributeValue($product, $erp_product_iteration ,"Size and care" ), 
                ],
                [
                    "attribute_id" => 30,
                    "value" => $ean_code_value, 
                ],
            ];

            foreach ( $attribute_data as $attributeData )
            {
                $attribute = Attribute::find($attributeData["attribute_id"]);
                $attribute_type = config("attribute_types")[$attribute->type ?? "string"];
                $value = $attribute_type::create(["value" => $attributeData["value"]]);

                $product_attribute_data = [
                    "attribute_id" => $attribute->id,
                    "product_id"=> $product->id,
                    "value_type" => $attribute_type,
                    "value_id" => $value->id,
                    "scope" => "website",
                    "scope_id" => 1
                ];
                $match = $product_attribute_data;
                unset($match["value_id"]);

                ProductAttribute::updateOrCreate($match, $product_attribute_data);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function getAttributeOptionValue(mixed $erp_product_iteration, string $attribute_slug): bool
    {
        try
        {
            switch ($attribute_slug) {
                case 'color':
                    $data = [
                        "attribute_id" => Attribute::whereSlug("color")->first()->id,
                        "name" => $erp_product_iteration->value["webAssortmentColor_Description"],
                        "code" => $erp_product_iteration->value["webAssortmentColor_Code"]
                    ];
                    $match = $data;
                    unset($match["name"]);
                    $attribute_option = AttributeOption::updateOrCreate($match, $data);
                    break;
                
                case 'size':
                    $data = [
                        "attribute_id" => Attribute::whereSlug("size")->first()->id,
                        "name" => $erp_product_iteration["pfHorizontalComponentCode"],
                        "code" => $erp_product_iteration["pfVerticalComponentCode"]
                    ];
                    $match = $data;
                    unset($match["code"]);
                    $attribute_option = AttributeOption::updateOrCreate($match, $data);
                    break;
            }
            
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $attribute_option->id;
    }

    //This fn is for concat features and size and care values 
    private function getAttributeValue(object $product, object $erp_product_iteration, string $attribute_name): string
    {
        try
        {
            $attribute_groups = $this->getDetailCollection("attributeGroups", $erp_product_iteration->sku);
            $attach_value = "";
            if ( $attribute_groups->count() > 1 )
            {
                $this->getValue($attribute_groups, function ($value) use (&$attach_value, $attribute_name) {
                    if ( $value["attributetype"] == $attribute_name ) $attach_value .= Str::finish($value["description"], ".\r\n ");
                });
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        return $attach_value;
    }

    // This fn will get value form erp detail value field 
    private function getValue(mixed $values, callable $callback = null): mixed
    {
        try
        {
            $data = $values->map( function ($value) use($callback){
                if ( $callback ) $data = $callback($value->value);
                return ( $callback ) ? $data : $value->value;
            });
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data;
    }

    private function mapstoreImages( object $product, object $erp_product_iteration, array $variant = [] ): void
    {
        $product_images = $this->getDetailCollection("productImages", $erp_product_iteration->sku); 
        $images = $this->getValue($product_images, function ($value) {
            return json_decode($value, true) ?? $value;
        });

        if ( !empty($variant) ) $images = $images->where("color_code", $variant["pfVerticalComponentCode"]);

        if ( $images->count() > 0 )
        {
            $position = 0;
            foreach( $images as $image )
            {
                $position++;
                $data["path"] = $image["url"];
                $data["position"] = $position;
                $data["product_id"] = $product->id;
                
                switch ( $image["image_type"] )
                {
                    case "a" :
                        $data["main_image"] = 1;
                    break;
        
                    case "b" :
                        $data["small_image"] = 1;
                    break;
        
                    default :
                        $data["thumbnail"] = 1;
                    break;
                }
                
                ProductImage::updateOrCreate($data);
            }
        }
    }
    
    // This fn create variants based on parent product
    private function createVariants( object $product, object $erp_product_iteration ): void
    {
        try
        {
            $variants = $this->getDetailCollection("productVariants", $erp_product_iteration->sku);

            if ( $variants->count() > 1 )
            {		
                $ean_codes = $this->getDetailCollection("eanCodes", $erp_product_iteration->sku);
                foreach ( $this->getValue($variants) as $variant )
                {
                    $product_data = [
                        "parent_id" => $product->id,
                        "attribute_set_id" => 1,
                        "website_id" => 1,
                        "sku" => "{$product->sku}-{$variant['code']}",
                        "type" => "simple",
                    ];

                    $match = [
                        "website_id" => 1,
                        "sku" => "{$product->sku}-{$variant['code']}",
                    ];

                    $variant_product = Product::updateOrCreate($match, $product_data);
                    
                    $ean_code = $this->getValue($ean_codes)->where("variantCode", $variant["code"])->first()["crossReferenceNo"] ?? "" ;
        
                    ErpMigrateProductImageJob::dispatch($variant_product, $erp_product_iteration, $variant);
                    ErpMigrateProductAttributeJob::dispatch($variant_product, $erp_product_iteration, $ean_code, 8, $variant);
                    ErpMigrateProductInventoryJob::dispatch($variant_product, $erp_product_iteration);
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function createInventory(object $product, object $erp_product_iteration): void
    {
        try
        {
            $inventory = $this->getDetailCollection("webInventories", $erp_product_iteration->sku);

            if ( $inventory->count() > 1 )
            {
                $value = array_sum($this->getValue($inventory)->pluck("Inventory")->toArray());
                $data = [
                    "quantity" => $value,
                    "use_config_manage_stock" => 1,
                    "product_id" => $product->id,
                    "website_id" => $product->website_id,
                    "manage_stock" =>  0,
                    "is_in_stock" => (bool) ($value > 0) ? 1 : 0,
                ];
        
                $match = [
                    "product_id" => $product->id,
                    "website_id" => $product->website_id
                ];
    
                CatalogInventory::updateOrCreate($match, $data);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function getDetailCollection(string $slug, string $sku): Collection
    {
        return ErpImport::where("type", $slug)->first()->erp_import_details()->where("sku", $sku)->get();
    }
}
