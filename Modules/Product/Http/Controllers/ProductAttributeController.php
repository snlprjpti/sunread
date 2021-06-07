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
use Illuminate\Support\Facades\Request as FacadesRequest;
use Modules\Core\Rules\ConfigurationRule;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Repositories\ProductAttributeRepository;
use Modules\Product\Rules\ScopeRule;
use Modules\Product\Transformers\ProductAttributeResource;

class ProductAttributeController extends BaseController
{
    protected $repository;

    public function __construct(ProductAttribute $productAttribute, ProductAttributeRepository $productAttributeRepository)
    {
        $this->model = $productAttribute;
        $this->model_name = "Product Attribute";
        $this->repository = $productAttributeRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(array $data): ResourceCollection
    {
        return ProductAttributeResource::collection($data);
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $rules = (isset($request->scope) && isset($request->scope_id)) ? [
                "scope" => [ "required", "in:channel,store" ],
                "scope_id" => ["required", "integer", "min:1", new ScopeRule($request->scope)]
            ] : [];
            
            $data = $this->repository->validateData($request, $rules);
            $attributes = $this->repository->validateAttributes(new Request($data));
            $created = $this->repository->createOrUpdate($attributes, $data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($created), $this->lang('create-success'), 201);
    }

}
