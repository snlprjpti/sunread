<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class ElasticSearchRepository 
{
    protected $scope, $path, $storeORGlobal;

    public function getScope($request)
    {
        return isset($request->store_id) ? "store.$request->store_id" : 
        (isset($request->channel_id) ? "channel.$request->channel_id" : "global"); 
    }

    public function getPath($request)
    {
        return isset($request->store_id) ? "store" : 
        (isset($request->channel_id) ? "channel" : "global"); 
    }

    public function getStore($request)
    {
        return isset($request->store_id) ? $request->store_id : "global"; 
    }

    public function orwhereQuery(array $filter)
    {
        return [
            "bool"=> [
                "should"=>  $filter
            ]
        ]; 
    }

    public function whereQuery(array $filter)
    {
        return [
            "bool"=> [
                "must"=>  $filter
            ]
        ]; 
    }

    public function queryString(array $field, string $data)
    {
        return [
            "query_string" => [
                "query" => '*'.$data.'*',
                "fields" => $field
            ]
        ];
    }

    public function match(string $field, string $data)
    {
        return [
            "match" => [
                $field => $data
            ]
        ];
    }

    public function term(string $field, string $data)
    {
        return [
            "term" => [
                $field => $data
            ]
        ];
    }

    public function range(string $field, $value1, $value2)
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
