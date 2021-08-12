<?php

namespace Modules\Product\Traits\ElasticSearch;

use Elasticsearch\ClientBuilder;
use Exception;
use Modules\Core\Entities\Website;

trait HasIndexing
{
    protected $client;

    public function setIndexName(int $id)
    {
        $prefix = config("elastic.prefix");
        return $prefix.$id;
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
                "body" => $product->documentDataStructure($this->store)
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
            $params["index"]  = $this->setIndexName($store->id);
            $exists = $this->checkIndexIfExist($params);

            if ($exists) {
                $params = array_merge($params, [ 
                    "id" => $product->id,
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

    public function removeIndex(object $product, object $store): void
    {
        try
        {
            $params["index"]  = $this->setIndexName($store->id);
            $exists = $this->checkIndexIfExist($params);

            if ($exists) {
                $params = array_merge($params, [ "id" => $product["id"] ]);
                $this->client->delete($params);
            }
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
}
