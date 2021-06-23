<?php

namespace Modules\Attribute\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Exceptions\AttributeGroupsPresent;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Repositories\AttributeGroupRepository;
use Modules\Attribute\Repositories\AttributeSetRepository;
use Modules\Attribute\Transformers\AttributeResource;
use Modules\Attribute\Transformers\AttributeSetResource;

class AttributeSetController extends BaseController
{
    protected $repository, $attributeGroupRepository;

    public function __construct(AttributeSetRepository $attributeSetRepository, AttributeSet $attribute_set, AttributeGroupRepository $attributeGroupRepository)
    {
        $this->repository = $attributeSetRepository;
        $this->model = $attribute_set;
        $this->model_name = "Attribute Set";
        $exception_statuses = [
            DefaultFamilyCanNotBeDeleted::class => 403,
            AttributeGroupsPresent::class => 403
        ];

        $this->attributeGroupRepository = $attributeGroupRepository;

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function collection(object $data): ResourceCollection
    {
        return AttributeSetResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new AttributeSetResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, [ "attribute_groups.attributes" ]);
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
            $this->repository->attributeValidation($data);
            
            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);
            $created = $this->repository->create($data, function($created) use ($request) {
                if(isset($request->groups)) $this->attributeGroupRepository->updateOrCreate($request->groups, $created);
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
            $fetched = $this->repository->fetch($id, [ "attribute_groups.attributes" ]);
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
                "slug" => "nullable|unique:attribute_sets,slug,{$id}"
            ]);
            $this->repository->attributeValidation($data);

            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);
            $updated = $this->repository->update($data, $id, function($updated) use ($request) {
                if(isset($request->groups)) $this->attributeGroupRepository->updateOrCreate($request->groups, $updated, "update");
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
            $this->repository->delete($id, function($deleted) {
                if ($deleted->slug == 'default') throw new DefaultFamilyCanNotBeDeleted($this->lang('response.default-set-delete'));
                if ( count($deleted->attribute_groups) > 0 ) throw new AttributeGroupsPresent($this->lang('response.attribute-groups-present'));
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

    public function unassignedAttributes(int $id): JsonResponse
    {
        try
        {
            $data = $this->model->findOrFail($id);

            $attribute_ids = $data->attribute_groups->map(function($attributeGroup){
                return $attributeGroup->attributes->pluck('id');
            })->flatten(1)->toArray();
            $fetched = Attribute::whereNotIn('id', $attribute_ids)->get();
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse(AttributeResource::collection($fetched), $this->lang('fetch-success'));
    }

    public function listAttributeSets(): JsonResponse
    {
        try
        {
            $fetched = $this->model::all();
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function attributeSet(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request, [
                "product" => ($request->product) ? "required|integer|exists:products,id" : "nullable",
                "attribute_set" => ($request->product) ? "nullable" : "required|integer"
            ]);

            $product = Product::find($request->product);
            $data = $this->model->findOrFail($product ? $product->attribute_set_id : $request->attribute_set);
            $groups = [];

            $attribute_groups = $data->attribute_groups->sortBy("position")->map(function ($attribute_group) use (&$groups, $product) {                
                $groups[] = [
                    "group_id" => $attribute_group->id,
                    "title" => $attribute_group->name,
                    "position" => $attribute_group->position,
                    "elements" => $attribute_group->attributes->sortBy("position")->map(function ($attribute) use ($product){
                        return [
                            "attribute_id" => $attribute->id,
                            "name" => $attribute->name,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required,
                            "type" => $attribute->type,
                            "value" => $product ? $attribute->product_attributes->where("product_id", $product->id)->first()->value_data ?? '' : '' 
                        ];
                    })->toArray()
                ];
            });

            $default = [
                "general" => [
                    [
                        "title" => "General Details",
                        "position" => 1,
                        "elements" => [
                            [
                                "title" => "SKU",
                                "name" => "sku",
                                "is_required" => 1,
                                "position" => 1,
                                "type" => "text",
                                "value" => $product->sku ?? '' 
                            ],
                            [
                                "title" => "Type",
                                "name" => "type",
                                "is_required" => 1,
                                "position" => 2,
                                "type" => "hidden",
                                "value" => $product->type ?? ''
                            ],
                            [
                                "title" => "Attribute Set",
                                "name" => "attribute_set_id",
                                "is_required" => 1,
                                "position" => 3,
                                "type" => "select",
                                "value" => $product ? $product->attribute_set_id : $request->attribute_set_id
                            ]
                        ]
                    ]
                ],
                "attribute_groups" => $groups,
            ];
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($default, $this->lang("fetch-success"));
    }
}
