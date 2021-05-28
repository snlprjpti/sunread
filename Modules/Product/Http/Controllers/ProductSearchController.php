<?php

namespace Modules\Product\Http\Controllers;

use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Product;

class ProductSearchController extends BaseController
{
    protected $repository;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->model_name = "Product";

        parent::__construct($this->model, $this->model_name);
    }

    public function search(Request $request): JsonResponse
    {
        $filter = isset($request->store_id) ? "store.$request->store_id" : (isset($request->channel_id) ? "channel.$request->channel_id" : "global"); 

        $fetched = $this->model->searchRaw([
          //  "_source" => ["id","parent_id","brand_id","attribute_group_id","sku","type","created_at","updated_at", "categories","channels", "product_attributes.$filter"],
            "query"=> [
                "bool"=> [
                  "should"=> [
                    ["query_string" => [
                      "query" => '*'.$request->search.'*',
                      "fields"=> ["sku"]
                      ]
                    ],
                    [
                      "nested" => [
                      "path" => "product_attributes.global",
                      "score_mode" => "avg",
                      "query"=> [
                        "query_string" => [
                          "query" => '*'.$request->search.'*',
                          "fields"=> ["product_attributes.global.slug.attribute.value"]
                        ]
                      ]
                  ]
                    ]
                  ]
                ]
              ]
        ]);
        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function productWithFilters(Request $request): JsonResponse
    {
        $filter = isset($request->store_id) ? "store.$request->store_id" : (isset($request->channel_id) ? "channel.$request->channel_id" : "global"); 

        $fetched = $this->model->searchRaw([
            "_source" => ["id","parent_id","brand_id","attribute_group_id","sku","type","created_at","updated_at", "product_attributes.$filter", "categories"],
            "query"=> [
                "bool"=> [
                  "must"=> [
                    // ["term" => [
                    //   "attribute_group_id" => $request->attribute_group_id,
                    //   ]
                    // ],
                    // ["term" => [
                    //     "categories.$request->category_id.global.id" => $request->category_id,
                    //     ]
                    // ],
                     [
                      "nested"=> [
                        "path"=> "product_attributes",
                        "query"=> [
                            "nested"=> [
                                "path"=> "product_attributes.global",
                                "query"=> [
                                    "bool"=> [
                                        "must"=> [
                                        //    [
                                        //     "term"=> [
                                        //       "product_attributes.global.slug.attribute.value"=> "FirstGlobalSlug"
                                        //     ]
                                        //   ],
                                           [
                                            "range"=> [
                                              "product_attributes.global.price.attribute.value"=> [
                                                "gte"=> $request->min_price,
                                                "lte"=> $request->max_price,
                                              ]
                                            ]
                                          ]
                                        ]
                                      ]
                                ]
                            ]
                        ]
                      ]
                    ]
                  ]
                ]
              ]
        ]);
        
        // $fetched = collect($fetched["hits"]["hits"]);
        
        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function reIndex($id)
    {
        try
        {
            $this->repository->reIndex($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse([], "", 200);
    }

    public function bulkReIndex(Request $request)
    {
        try
        {
            $this->repository->bulkReIndex($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse([], "", 200);
    }
}
