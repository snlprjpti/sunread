<?php

namespace Modules\Product\Repositories;

class ElasticSearchRepository 
{
    protected $scope, $path, $storeORGlobal;

    public function getScope($request): string
    {
        return isset($request->store_id) ? "store.$request->store_id" : 
        (isset($request->channel_id) ? "channel.$request->channel_id" : "global"); 
    }

    public function getPath($request): string
    {
        return isset($request->store_id) ? "store" : 
        (isset($request->channel_id) ? "channel" : "global"); 
    }

    public function getStore($request): string
    {
        return isset($request->store_id) ? "store.$request->store_id" : "global"; 
    }

    public function orwhereQuery(array $filter): array
    {
        return [
            "bool"=> [
                "should"=>  $filter
            ]
        ]; 
    }

    public function whereQuery(array $filter): array
    {
        return [
            "bool"=> [
                "must"=>  $filter
            ]
        ]; 
    }

    public function queryString(array $field, string $data): array
    {
        return [
            "query_string" => [
                "query" => '*'.$data.'*',
                "fields" => $field
            ]
        ];
    }

    public function match(string $field, string $data): array
    {
        return [
            "match" => [
                $field => $data
            ]
        ];
    }

    public function term(string $field, string $data): array
    {
        return [
            "term" => [
                $field => $data
            ]
        ];
    }

    public function range(string $field, $value1, $value2): array
    {
        return [
            "range"=> [
                $field=> [
                "gte"=> $value1,
                "lte"=> $value2
                ]
            ]
        ];
    }
}
