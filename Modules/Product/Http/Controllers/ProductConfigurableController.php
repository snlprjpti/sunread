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
use Modules\Core\Rules\ScopeRule;
use Modules\Product\Repositories\ProductRepository;

class ProductConfigurableController extends BaseController
{
    protected $repository, $product_repository;

    public function __construct(Product $product, ProductConfigurableRepository $productConfigurableRepository, ProductRepository $product_repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $productConfigurableRepository;
        $this->product_repository = $product_repository;
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
            $data = $this->repository->validateData($request, [
                "website_id" => "required|exists:websites,id",
                "attribute_set_id" => "required|exists:attribute_sets,id",
                "scope_id" => ["sometimes", "integer", "min:0", new ScopeRule($request->scope)]
            ], function ($request) {
                return [
                    "scope" => $request->scope ?? "website",
                    "scope_id" => $request->scope_id ?? $request->website_id,
                    "type" => "configurable"
                ];
            });
            
            $scope = [
                "scope" => $data["scope"],
                "scope_id" => $data["scope_id"]
            ];

            $created = $this->repository->create($data, function (&$created) use ($request, $scope) {
                $attributes = $this->repository->validateAttributes($created, $request, $scope);
                $this->repository->syncAttributes($attributes, $created, $scope);

                $this->repository->attributeMapperSync($created, $request);

                $created->channels()->sync($request->get("channels"));

                $this->repository->createVariants($created, $request, $scope, $attributes);
            });
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
                "scope_id" => ["sometimes", "integer", "min:0", new ScopeRule($request->scope)]
            ], function ($request) use($id) {
                return [
                    "scope" => $request->scope ?? "website",
                    "scope_id" => $request->scope_id ?? $this->model::findOrFail($id)->website_id,
                    "type" => "configurable"
                ];
            });

            $scope = [
                "scope" => $data["scope"],
                "scope_id" => $data["scope_id"]
            ];

            $updated = $this->repository->update($data, $id, function($updated) use($request, $scope) {
                $attributes = $this->repository->validateAttributes($updated, $request, $scope);
                $this->repository->syncAttributes($attributes, $updated, $scope);

                $this->repository->attributeMapperSync($updated, $request);

                $updated->channels()->sync($request->get("channels"));

                $this->repository->createVariants($updated, $request, $scope, $attributes);
            });
        }
        catch(Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

}
