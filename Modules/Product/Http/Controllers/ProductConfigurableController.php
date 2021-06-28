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
            $product = $this->model::findOrFail($id);
            $data = $this->repository->validateData($request, [
                "sku" => "required|unique:products,sku,{$id}",
                "brand_id" => "sometimes|nullable|exists:brands,id",
                "website_id" => "required|exists:websites,id",
                "super_attributes" => "required|array",
                "attributes" => "required|array",
                "categories" => "required|array",
                "categories.*" => "required|exists:categories,id"
            ]);

            if ( $product->attribute_set_id != $request->attribute_set_id ) throw new ProductAttributeCannotChangeException("Attribute set cannot change.");

            // check attributes and validated request attrubutes.
            $this->repository->checkAttribute($product->attribute_set_id, $request);
            
            //get previous keys and delete variants
            foreach ($product->variants->pluck('id') as $variantId) $this->model::find($variantId)->delete();

            $super_attributes = [];
            //create product-superattribute
            foreach ($request->super_attributes as $attributeCode => $attributeOptions) {
                $attribute = Attribute::whereSlug($attributeCode)->first();
                $super_attributes[$attribute->id] = $attributeOptions;
            }
            
            //generate multiple product(variant) combination on the basis of color and size for variants
            foreach (array_permutation($super_attributes) as $permutation) {
                $this->createVariant($product, $permutation, $request);
            }

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

    public function createVariant(object $product, mixed $permutation, object $request): object
    {
        try 
        {
            $data = [
                "parent_id" => $product->id,
                "website_id" => $product->website_id,
                "brand_id" => $product->brand_id,
                "type" => "simple",
                "attribute_set_id" => $product->attribute_set_id,
                "sku" => \Str::slug($product->sku) . "-variant-" . implode("-", $permutation),
            ];
    
            $product_variant = $this->repository->create($data, function ($variant) use ($product, $permutation, $request){    
                $product_attribute = [
                    [
                        // Attrubute slug
                        "attribute_id" => 1,
                        "value" => \Str::slug($product->sku) . "-variant-" . implode("-", $permutation),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ],
                    [
                        // Attrubute name
                        "attribute_id" => 2,
                        "value" => $product->sku . "-variant-" . implode("-", $permutation),
                        "value_type" => "Modules\Product\Entities\ProductAttributeString"
                    ]
                ];
                $this->repository->syncAttributes($product_attribute, $variant);
                
                $variant->categories()->sync($request->get("categories"));
                $variant->channels()->sync($request->get("channels"));
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $product_variant;
    }
}
