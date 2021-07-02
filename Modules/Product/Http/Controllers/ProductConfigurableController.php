<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Repositories\ProductConfigurableRepository;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Transformers\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Validation\ValidationException;
use Modules\Product\Exceptions\ProductAttributeCannotChangeException;
use Exception;

class ProductConfigurableController extends BaseController
{
    protected $repository;

    public function __construct(Product $product, ProductConfigurableRepository $productRepository)
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

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $data["type"] = "configurable";
            $created = $this->repository->create($data);
        }
        catch(Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "sku" => "required|unique:products,sku,{$id}",
                "brand_id" => "sometimes|nullable|exists:brands,id",
                "website_id" => "required|exists:websites,id",
                "super_attributes" => "required|array",
                "attributes" => "required|array",
                "categories" => "required|array",
                "categories.*" => "required|exists:categories,id",
                "attribute_set_id" => "sometimes|nullable"
            ]);  

            $product = $this->repository->fetch($id);
            
            // check attributes and validated request attrubutes.
            $this->repository->checkAttribute($product->attribute_set_id, $request);

            // create product variants based on super attributes and parent product.
            $this->repository->createVariants($product, $request);

            unset($data["attribute_set_id"]);

            $updated = $this->repository->update($data, $id, function($updated) use($request) {
                $attributes = $this->repository->validateAttributes($request);
                $this->repository->syncAttributes($attributes, $updated);
                $updated->categories()->sync($request->get("categories"));
                $updated->channels()->sync($request->get("channels"));
            });
        }
        catch(Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

}
