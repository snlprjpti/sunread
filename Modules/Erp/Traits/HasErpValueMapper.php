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
use Modules\Core\Entities\Channel;
use Modules\Erp\Entities\ErpImportDetail;
use Modules\Erp\Jobs\Mapper\ErpDetailStatusUpdate;
use Modules\Erp\Jobs\Mapper\ErpGenerateVariantProductJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateAttributeConfigurableProduct;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductImageJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductAttributeJob;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductInventoryJob;
use Modules\Erp\Jobs\Mapper\ErpMigratorJob;
use Modules\Product\Entities\AttributeConfigurableProduct;
use Modules\Product\Entities\AttributeOptionsChildProduct;

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
            $count = 0;
            foreach ( $chunked as $chunk )
            {
                foreach ( $chunk as $detail )
                {
                    if ( $detail->status == 1 ) continue;
                    if ( $detail->value["webAssortmentWeb_Active"] == false ) continue;
                    if ( $detail->value["webAssortmentWeb_Setup"] != "SR" ) continue;
                    //loop breaked for testing
                    if ( $count == 10 ) break;
                    ErpMigratorJob::dispatch($detail);
                    $count++;
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
            
            // get price for specific product
            $price = $this->getDetailCollection("salePrices", $erp_product_iteration->sku);
            $default_price_data = [
                "unitPrice" => 0.0,
                "startingDate" => "",
                "endingDate" => ""
            ];
            $this->storeScopeWiseValue($price, $product);
            $price_value = ($price->count() > 1) ? $this->getValue($price)->where("currencyCode", "USD")->where("salesCode", "WEB")->first() ?? $default_price_data : $default_price_data;

            // Condition for invalid date/times
            $max_time = strtotime("2030-12-28");
            $start_time = abs(strtotime($price_value["startingDate"]));
            $end_time = abs(strtotime($price_value["endingDate"]));

            $start_time = $start_time < $max_time ? $start_time : $max_time - 1;
            $end_time = $end_time < $max_time ? $end_time : $max_time;

            $attribute_data = [
                [
                    "attribute_id" => $this->getAttributeId("name"),
                    "value" => $erp_product_iteration->value["description"]
                ],
                [
                    "attribute_id" => $this->getAttributeId("price"),
                    "value" => ($variants->count() > 1) ? $price_value["unitPrice"] : "", 
                ],
                [
                    "attribute_id" => $this->getAttributeId("cost"),
                    "value" => ($variants->count() > 1) ? $price_value["unitPrice"] : "", 
                ],
                [
                    "attribute_id" => $this->getAttributeId("special_from_date"),
                    "value" => Carbon::parse(date("Y-m-d", $start_time)), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("special_to_date"),
                    "value" => Carbon::parse(date("Y-m-d", $end_time)), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("visibility"),
                    "value" => $visibility, 
                ],
                [
                    "attribute_id" => $this->getAttributeId("description"),
                    "value" => $description ?? $erp_product_iteration->value["description"], 
                ],
                [
                    "attribute_id" => $this->getAttributeId("short_description"),
                    "value" => Str::limit($description, 100), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("url_key"),
                    "value" => Str::slug($product->sku), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("meta_keywords"),
                    "value" => $description ?? $erp_product_iteration->value["description"], 
                ],
                [
                    "attribute_id" => $this->getAttributeId("meta_title"),
                    "value" => $erp_product_iteration->value["description"], 
                ],
                [
                    "attribute_id" => $this->getAttributeId("meta_description"),
                    "value" => $description ?? $erp_product_iteration->value["description"], 
                ],
                [
                    "attribute_id" => $this->getAttributeId("status"),
                    "value" => 1, 
                ],
                [
                    "attribute_id" => $this->getAttributeId("color"),
                    "value" => ($this->getDetailCollection("productVariants", $erp_product_iteration->sku)->count() > 1) ? $this->getAttributeOptionValue($erp_product_iteration, "color") : "", 
                ],
                [
                    "attribute_id" => $this->getAttributeId("size"),
                    "value" => ($this->getDetailCollection("productVariants", $erp_product_iteration->sku)->count() > 1) ? $this->getAttributeOptionValue($variant, "size") : "", 
                ],
                [
                    "attribute_id" => $this->getAttributeId("features"),
                    "value" => $this->getAttributeValue($product, $erp_product_iteration ,"Features" ), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("size-and-care"),
                    "value" => $this->getAttributeValue($product, $erp_product_iteration ,"Size and care" ), 
                ],
                [
                    "attribute_id" => $this->getAttributeId("ean-code"),
                    "value" => $ean_code_value, 
                ],
            ];

            foreach ( $attribute_data as $attributeData )
            {
                if (empty($attributeData["value"])) continue;
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

    public function getAttributeId(string $slug): ?int
    {
        try
        {
            $attribute_id = Attribute::whereSlug($slug)->first()?->id;
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $attribute_id;
    }

    private function storeScopeWiseValue(mixed $prices, object $product): bool
    {
        try
        {
            $price_data = $this->getValue($prices)->filter(function ($price_value) {
                return $price_value["salesCode"] == "WEB" && $price_value["currencyCode"] !== "USD";
            })->map(function ($price_value) {

                // Condition for invalid date/times
                $max_time = strtotime("2030-12-28");
                $start_time = abs(strtotime($price_value["startingDate"]));
                $end_time = abs(strtotime($price_value["endingDate"]));

                $start_time = $start_time < $max_time ? $start_time : $max_time - 1;
                $end_time = $end_time < $max_time ? $end_time : $max_time;

                return [ 
                    [
                        "attribute_id" => $this->getAttributeId("price"),
                        "value" => $price_value["unitPrice"],
                        "channel_code" => empty($price_value["currencyCode"]) ? "SEK" : $price_value["currencyCode"] 
                    ],
                    [
                        "attribute_id" => $this->getAttributeId("special_from_date"),
                        "value" => Carbon::parse(date("Y-m-d", $start_time)),
                        "channel_code" => empty($price_value["currencyCode"]) ? "SEK" : $price_value["currencyCode"]
                    ],
                    [
                        "attribute_id" => $this->getAttributeId("special_to_date"),
                        "value" => Carbon::parse(date("Y-m-d", $end_time)),
                        "channel_code" => empty($price_value["currencyCode"]) ? "SEK" : $price_value["currencyCode"]
                    ] 
                ];
            });

            foreach ( $price_data as $price )
            {
                foreach ($price as $attributeData)
                {
                    $channel_id  = $this->getChannelId($attributeData["channel_code"])->id ?? 1;
                    $attribute = Attribute::find($attributeData["attribute_id"]);
                    $attribute_type = config("attribute_types")[$attribute->type ?? "string"];
                    $value = $attribute_type::create(["value" => $attributeData["value"]]);

                    $product_attribute_data = [
                        "attribute_id" => $attribute->id,
                        "product_id"=> $product->id,
                        "value_type" => $attribute_type,
                        "value_id" => $value->id,
                        "scope" => "channel",
                        "scope_id" => $channel_id
                    ];
                    $match = $product_attribute_data;
                    unset($match["value_id"]);
                    ProductAttribute::updateOrCreate($match, $product_attribute_data);  
                }              
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

    private function getChannelId(string $code): ?object
    {
        try
        {
            $data = [
                "name" => $code,
                "code" => $code,
                "hostname" => "{$code}.xyz.co",
                "description" => "{$code} channel",
                "website_id" => 1
            ];
            $match = $data;
            unset($match["description"], $match["name"]);
            $channel = Channel::updateOrCreate($match, $data);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $channel;
    }

    private function getAttributeOptionValue(mixed $erp_product_iteration, string $attribute_slug): ?int
    {
        try
        {
            switch ($attribute_slug) {
                case 'color':
                    $data = [
                        "attribute_id" => $this->getAttributeId("color"),
                        "name" => $erp_product_iteration->value["webAssortmentColor_Description"] ?? "",
                        "code" => $erp_product_iteration->value["webAssortmentColor_Code"] ?? ""
                    ];
                    $attribute_option = AttributeOption::updateOrCreate($data);
                    break;
                
                case 'size':
                    $data = [
                        "attribute_id" => $this->getAttributeId("size"),
                        "name" => $erp_product_iteration["pfHorizontalComponentCode"] ?? ""
                    ];
                    $attribute_option = AttributeOption::updateOrCreate($data);
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
        return nl2br($attach_value);
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
        try
        {
            $product_images = $this->getDetailCollection("productImages", $erp_product_iteration->sku); 
            $images = $this->getValue($product_images, function ($value) {
                return is_array($value) ? $value : json_decode($value, true) ?? $value;
            });
    
            if ( !empty($variant) ) $images = $images->filter(function ($image) use ($variant) {
                return $image["color_code"] == $variant["pfVerticalComponentCode"] || $image["color_code"] == $variant["pfHorizontalComponentCode"];                   
            });
    
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
                            $type_id = 1;
                        break;
            
                        case "b" :
                            $type_id = 2;
                        break;
            
                        case "c" :
                            $type_id = 3;
                        break;
    
                        case "d" :
                            $type_id = 4;
                        break;
    
                        default :
                            $type_id = 5;
                        break;
                    }
                    
                   $product_image = ProductImage::updateOrCreate($data);
                   $product_image->types()->sync($type_id);
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
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
                $variant_product_ids = [];
                foreach ( $this->getValue($variants) as $variant )
                {
                    $product_data = [
                        "parent_id" => $product->id,
                        "attribute_set_id" => 1,
                        "website_id" => 1,
                        "sku" => "{$product->sku}_{$variant['code']}",
                        "type" => "simple",
                    ];

                    $match = [
                        "website_id" => 1,
                        "sku" => "{$product->sku}_{$variant['code']}",
                    ];

                    $variant_product = Product::updateOrCreate($match, $product_data);
                    $variant_product_ids[] = $variant_product->id;
                    $ean_code = $this->getValue($ean_codes)->where("variantCode", $variant["code"])->first()["crossReferenceNo"] ?? "" ;
        
                    $attribute_option = AttributeOption::whereCode($variant['pfVerticalComponentCode'])
                    ->orWhere('name', $variant['pfHorizontalComponentCode'])
                    ->first();

                    $this->createAttributeValue($variant_product, $erp_product_iteration, $ean_code, 8, $variant);
                    if ($attribute_option)
                    {
                        $configurable_product_attributes["product_id"] = $product->id;
                        $configurable_product_attributes["attribute_id"] = $attribute_option?->attribute_id;

                        $configurable_product_attributes["used_in_grouping"] = ($attribute_option?->attribute_id == $this->getAttributeId("color")) ? 1 : 0;
                        AttributeConfigurableProduct::updateOrCreate($configurable_product_attributes);
                        AttributeOptionsChildProduct::updateOrCreate([
                            "attribute_option_id" => $attribute_option?->id,
                            "product_id" => $variant_product->id 
                        ]);
                    }
                    $this->mapstoreImages($product, $erp_product_iteration, $variant);
                    $this->createInventory($variant_product, $erp_product_iteration);
                }

                $this->updateVisibility($variant_product_ids, $product);
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }

    private function updateVisibility(array $variant_product_ids, object $product): void
    {
        try
        {
            $attribute_configurable_product = AttributeConfigurableProduct::whereProductId($product->id)->get();
            if ( $attribute_configurable_product->count() == 1)
            {
                $product->product_attributes->where("attribute_id", $this->getAttributeId("visibility"))->first()?->value->update(["value" => 8]);
                $variant_products = Product::whereIn("id", $variant_product_ids)->with(["product_attributes"])->get();
                foreach ( $variant_products as $variant_pro ) $variant_pro->product_attributes->where("attribute_id", $this->getAttributeId("visibility"))->first()?->value->update(["value" => 5]);
            }
    
            $attr_option_products = AttributeOptionsChildProduct::whereIn("product_id", $variant_product_ids)
                ->with(["attribute_option", "attribute_option.attribute", "variant_product.product_attributes"])
                ->get()
                ->filter(function ($filter_attribute_option) {
                    return $filter_attribute_option->attribute_option->attribute->id == $this->getAttributeId("color");
                })->groupBy("attribute_option_id");

            foreach ( $attr_option_products as $attr_option_product )
            {
                foreach ($attr_option_product->pluck("variant_product") as $key => $variant_product)
                {
                    if ($key == 0) continue;
                    $variant_product->product_attributes->where("attribute_id", $this->getAttributeId("visibility"))->first()?->value->update(["value" => 5]);
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
                    "is_in_stock" => ($value > 0) ? 1 : 0,
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
