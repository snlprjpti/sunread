<?php

namespace Modules\Product\Traits\ElasticSearch;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;

trait HasIndexing
{
    protected $client;

    public function setIndexName($id)
    {
        $prefix = config("elastic.prefix");
        return $prefix.$id;
    }

    public function connectElasticSearch(): void
    {
        $host = config("elastic.client.hosts");
        $this->client = ClientBuilder::create()->setHosts($host)->build();
    }

    public function createIndexIfNotExist(array $params): void
    {
        $this->connectElasticSearch();

        $exists = $this->client->indices()->exists($params);

        if (!$exists) {
            $createparams = array_merge($params, [
                "body" => [
                    "mappings" => config("mapping")
                ]
            ]);
            $this->client->indices()->create($createparams);
        }
    }

    public function singleIndexing($product, $store): void
    {
        $params["index"]  = $this->setIndexName($store->id);
        $this->createIndexIfNotExist($params);

        $params = array_merge($params, [
            "id" => $product->id,
            "body" => $product->documentDataStructure($this->store)
        ]);
        $this->client->index($params);
    }

    public function bulkIndexing($products): void
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
}
