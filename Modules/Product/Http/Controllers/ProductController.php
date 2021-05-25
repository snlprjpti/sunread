<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Product\Entities\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Transformers\ProductResource;
use Modules\Product\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductController extends BaseController
{
    protected $repository;

    public function __construct(Product $product, ProductRepository $productRepository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $productRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return ProductResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new ProductResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
        $product = Product::find(10);
        $array = $product->toArray();

        $array['categories'] = $product->categories;

        $array['channels'] = $product->channels;

        $stores = $product->channels->map(function($channel){
            dd($channel);
            return[
                $channel->stores->id
            ];
        })->toArray();


        $array['product_attributes'] = [
            'global' => [],
            'channel' => [],
            'store' => []
        ];
        foreach($product->product_attributes as $data){
            $attribute_value = [
                $data->attribute->slug => isset($data->value->value) ? $data->value->value : ""
            ];

            if(!isset($data->store_id) && !isset($data->channel_id)) $array['product_attributes']['global'] = array_merge($array['product_attributes']['global'], $attribute_value);
            
            if(!isset($data->store_id) && isset($data->channel_id))
            {
                if(in_array($data->channel_id, $product->channels->pluck('id')->toArray())) $array['product_attributes']['channel'][$data->channel_id] = (array_key_exists ($data->channel_id, $array['product_attributes']['channel'])) ? array_merge($array['product_attributes'] ['channel'][$data->channel_id], $attribute_value) : $attribute_value;
            }

            if(isset($data->store_id) && !isset($data->channel_id)) 
            {
                if(in_array($data->store_id, $stores)) $array['product_attributes'] ['store'][$data->store_id] = (array_key_exists ($data->store_id, $array['product_attributes']['store'])) ? array_merge($array['product_attributes'] ['store'][$data->store_id], $attribute_value) : $attribute_value;
            }
        }

        dd($array);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);

            $created = $this->repository->create($data, function($created) use($request) {
                $attributes = $this->repository->validateAttributes($request);
                $this->repository->syncAttributes($attributes, $created);
                $created->categories()->sync($request->get("categories"));
                $created->channels()->sync($request->get("channels"));
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->model->with(["parent", "brand", "attribute_group", "product_attributes", "categories", "images"])->findOrFail($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }
    
    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "sku" => "required|unique:products,sku,{$id}"
            ]);

            $updated = $this->repository->update($data, $id, function($updated) use($request) {
                $attributes = $this->repository->validateAttributes($request);
                $this->repository->syncAttributes($attributes, $updated);
                $updated->categories()->sync($request->get("categories"));
                $updated->channels()->sync($request->get("channels"));
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function($deleted){
                $deleted->product_attributes()->each(function($product_attribute){
                    $product_attribute->delete();
                });
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }
}
