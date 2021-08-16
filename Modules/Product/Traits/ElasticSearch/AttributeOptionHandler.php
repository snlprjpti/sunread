<?php

namespace Modules\Product\Traits\ElasticSearch;

use Exception;

trait AttributeOptionHandler
{
    use HasIndexing;

    public function handlePartialModify(object $product, object $attribute, object $attribute_option, $method): void
    {
        try
        {
            $stores = $this->getStores($product);
            foreach($stores as $store)
            {
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $attribute->id
                ];
                $value = $product->value($match);
                
                ($method == "update") 
                ? $this->handleUpdate($value, $attribute_option, $attribute, $product, $store) 
                : $this->handleDelete($value, $attribute_option, $attribute, $product, $store);
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        } 
    }

    public function handleUpdate(mixed $value, object $attribute_option, object $attribute, object $product, object $store): void
    {
        
    }
}
