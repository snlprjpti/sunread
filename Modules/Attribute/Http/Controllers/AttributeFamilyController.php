<?php

namespace Modules\Attribute\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Attribute\Entities\AttributeFamily;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Attribute\Exceptions\AttributeGroupsPresent;
use Modules\Attribute\Transformers\AttributeFamilyResource;
use Modules\Attribute\Repositories\AttributeFamilyRepository;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;

class AttributeFamilyController extends BaseController
{
    protected $repository;

    public function __construct(AttributeFamilyRepository $attributeFamilyRepository, AttributeFamily $attribute_family)
    {
        $this->repository = $attributeFamilyRepository;
        $this->model = $attribute_family;
        $this->model_name = "Attribute Family";
        $exception_statuses = [
            DefaultFamilyCanNotBeDeleted::class => 403,
            AttributeGroupsPresent::class => 403
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function collection(object $data): ResourceCollection
    {
        return AttributeFamilyResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new AttributeFamilyResource($data);
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
            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

            $created = $this->repository->create($data);
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
                "slug" => "nullable|unique:attribute_families,slug,{$id}"
            ]);
            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

            $updated = $this->repository->update($data, $id);
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
