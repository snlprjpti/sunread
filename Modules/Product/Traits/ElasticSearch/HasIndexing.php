<?php

namespace Modules\Product\Traits\ElasticSearch;

use Elasticsearch\ClientBuilder;
use Exception;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

trait HasIndexing
{
    protected $client;

    public function setIndexName(int $id): string
    {
        $prefix = config("elastic.prefix");
        return "{$prefix}{$id}";
    }

    public function connectElasticSearch(): void
    {
        $host = config("elastic.client.hosts");
        $this->client = ClientBuilder::create()->setHosts($host)->build();
    }

    public function checkIndexIfExist(array $params): bool
    {
        try
        {
            $this->connectElasticSearch();

            $exists = $this->client->indices()->exists($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $exists;
    }

    public function checkDocumentIfExist(array $params): bool
    {
        try
        {
            $this->connectElasticSearch();

            $exists = $this->client->exists($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $exists;
    }

    public function createIndexIfNotExist(array $params): void
    {
        try
        {
            $exist = $this->checkIndexIfExist($params);

            if (!$exist) {
                $createparams = array_merge($params, [
                    "body" => [
                        "mappings" => config("mapping")
                    ]
                ]);
                $this->client->indices()->create($createparams);
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function singleIndexing(object $product, object $store): void
    {
        try
        {
            $params["index"]  = $this->setIndexName($store->id);
            $this->createIndexIfNotExist($params);

            $params = array_merge($params, [
                "id" => $product->id,
                "body" => $product->documentDataStructure($store)
            ]);
            $this->client->index($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function partialIndexing(object $product, object $store, array $slug_values): void
    {
        try
        {
            $params = [
                "index" => $this->setIndexName($store->id),
                "id" => $product->id
            ];
            $exists = $this->checkDocumentIfExist($params);

            if ($exists) {
                $params = array_merge($params, [ 
                    "body" => [
                        "doc" => $slug_values
                    ]
                ]);
                $this->client->update($params);
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function partialRemoving(object $product, object $store, array $slug_values, array $field_values): void
    {
        try
        {
            if(count($slug_values) > 0) $this->partialIndexing($product, $store, $slug_values);

            $params = [
                "index" => $this->setIndexName($store->id),
                "id" => $product->id
            ];
            $exists = $this->checkDocumentIfExist($params);

            if ($exists) {

                if (count($field_values) > 0) {
                    $fields = implode(";", $field_values);

                    $params = array_merge($params, [ 
                        'body'  => [
                            'script' => $fields 
                        ]
                    ]);
                    
                    $this->client->update($params);
                }
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function removeIndex(object $product, object $store): void
    {
        try
        {
            $params = [
                "index" => $this->setIndexName($store->id),
                "id" => $product["id"]
            ];
            $exists = $this->checkDocumentIfExist($params);

            if ($exists) $this->client->delete($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function bulkIndexing(object $products): void
    {
        try
        {
            foreach($products as $product)
            {
                $product->load("categories", "product_attributes", "catalog_inventories");
                $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                    return $channel->stores;
                });
    
                foreach($stores as $store)
                {
                    $createParams["index"] = $this->setIndexName($store->id);
                    $this->createIndexIfNotExist($createParams);
    
                    $params["body"][] = [
                        "index" => [
                            "_index" => $createParams["index"],
                            "_id" => $product->id
                        ]
                    ];
                
                    $params["body"][] = $product->documentDataStructure($store);
                }
            }
            $this->client->bulk($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function configurableIndexing(array $products): void
    {
        try
        {
            foreach($products as $store_products)
            {
                foreach($store_products as $store => $store_product)
                {
                    $createParams["index"] = $this->setIndexName($store);
                    $this->createIndexIfNotExist($createParams);
    
                    $params["body"][] = [
                        "index" => [
                            "_index" => $createParams["index"],
                            "_id" => $store_product["id"]
                        ]
                    ];
                
                    $params["body"][] = $store_product;
                }
            }
            if(count($products) > 0) $this->client->bulk($params);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function searchIndex(array $data, object $store): ?array
    {
        try
        {
            $params["index"]  = $this->setIndexName($store->id);
            $exists = $this->checkIndexIfExist($params);
    
            $params = array_merge($params, [ "body" => $data ]);         
            $response = $exists ? $this->client->search($params) : null; 
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
 
        return $response; 
    }

    public function getStoreId(): ?object
    {
        try
        {
            $store_code = array_key_exists("store", getallheaders()) ? getallheaders()["store"] : "english-store";
            $store = Store::whereCode($store_code)->first();
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $store;
	}

    public function getStores(object $product): ?object
    {
        try
        {
            $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $stores;
    } 

}
