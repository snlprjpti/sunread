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
use Modules\Product\Exceptions\ProductAttributeCannotChangeException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
use Illuminate\Validation\ValidationException;

class ProductController extends BaseController
{
    protected $repository;

    public function __construct(Product $product, ProductRepository $productRepository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $productRepository;
        $exception_statuses = [
            ProductAttributeCannotChangeException::class => 403
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
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
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request, ["product_attributes", "images"]);
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
            $this->repository->checkAttribute($request->attribute_set_id, $request);
            
            $data["type"] = "simple";
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
            $product = $this->model::findOrFail($id);            
            $data = $this->repository->validateData($request, [
                "sku" => "required|unique:products,sku,{$id}"
            ]);

            $this->repository->checkAttribute($product->attribute_set_id, $request);
            unset($data["attribute_set_id"]);

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

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try
        {
            $updated = $this->repository->updateStatus($request, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("status-updated"));
    }
}
