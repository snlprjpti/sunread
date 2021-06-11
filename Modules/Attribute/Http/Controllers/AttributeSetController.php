<?php

namespace Modules\Attribute\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Exceptions\AttributeGroupsPresent;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Repositories\AttributeSetRepository;
use Modules\Attribute\Transformers\AttributeSetResource;

class AttributeSetController extends BaseController
{
    protected $repository;

    public function __construct(AttributeSetRepository $attributeSetRepository, AttributeSet $attribute_set)
    {
        $this->repository = $attributeSetRepository;
        $this->model = $attribute_set;
        $this->model_name = "Attribute Family";
        $exception_statuses = [
            DefaultFamilyCanNotBeDeleted::class => 403,
            AttributeGroupsPresent::class => 403
        ];

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
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
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

            $attributes_id_array = collect($data["groups"])->pluck('attributes')->flatten(1)->toArray();
            if(count($attributes_id_array) > count(array_unique($attributes_id_array)))
            {
                throw ValidationException::withMessages(["attributes" => "Different attribute groups consisting of same aatributes"]);
            } 
            
            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);
            $created = $this->repository->create($data, function($created) use ($request) {
                if(isset($request->groups)) $this->repository->updateOrCreate($request->groups, $created);
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
            $fetched = $this->model->findOrFail($id);
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

            $attributes_id_array = collect($data["groups"])->pluck('attributes')->flatten(1)->toArray();
            if(count($attributes_id_array) > count(array_unique($attributes_id_array)))
            {
                throw ValidationException::withMessages(["attributes" => "Different attribute groups consisting of same aatributes"]);
            } 

            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

            $updated = $this->repository->update($data, $id, function($updated) use ($request) {
                if(isset($request->groups)) $this->repository->updateOrCreate($request->groups, $updated);
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
                if ($deleted->slug == 'default') throw new DefaultFamilyCanNotBeDeleted("Default family cannot be deleted.");
                if ( count($deleted->attributeGroups) > 0 ) throw new AttributeGroupsPresent("Attribute Groups present in family.");
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
