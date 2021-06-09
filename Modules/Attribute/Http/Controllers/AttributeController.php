<?php

namespace Modules\Attribute\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Attribute\Entities\Attribute;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Attribute\Transformers\AttributeResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Repositories\AttributeOptionRepository;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Repositories\AttributeTranslationRepository;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;
use Modules\Attribute\Exceptions\AttributeNotUserDefinedException;

class AttributeController extends BaseController
{
    protected $repository, $translation_repository, $option_repository;

    public function __construct(Attribute $attribute, AttributeRepository $attributeRepository, AttributeTranslationRepository $attributeTranslationRepository, AttributeOptionRepository $attributeOptionRepository)
    {
        $this->repository = $attributeRepository;
        $this->model = $attribute;
        $this->model_name = "Attribute";
        $exception_statuses = [
            AttributeTranslationDoesNotExist::class => 422,
            AttributeTranslationOptionDoesNotExist::class => 422,
            AttributeNotUserDefinedException::class => 403
        ];

        $this->translation_repository = $attributeTranslationRepository;
        $this->option_repository = $attributeOptionRepository;

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function collection(object $data): ResourceCollection
    {
        return AttributeResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new AttributeResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request, ["translations", "attribute_group"]);
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
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->name);
            if (!in_array($request->type, $this->repository->non_filterable_fields)) $data["is_filterable"] = 0;

            $this->repository->validateTranslation($request);

            $created = $this->repository->create($data, function($created) use ($request) {
                $this->translation_repository->updateOrCreate($request->translations, $created);
                if (in_array($request->type, $this->repository->non_filterable_fields)) $this->option_repository->updateOrCreate($request->attribute_options, $created);
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
            $fetched->translations();
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
                "slug" => "nullable|unique:attributes,slug,{$id}"
            ]);
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->name);
            if (!in_array($request->type, $this->repository->non_filterable_fields)) $data["is_filterable"] = 0;

            $this->repository->validateTranslation($request);

            $updated = $this->repository->update($data, $id, function($updated) use ($request) {
                $this->translation_repository->updateOrCreate($request->translations, $updated);
                if (in_array($request->type, $this->repository->non_filterable_fields)) $this->option_repository->updateOrCreate($request->attribute_options, $updated);
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
                if (!$deleted->is_user_defined) throw new AttributeNotUserDefinedException("Attribute delete action not authorized.");
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try
        {
            $unauthorized_delete_count = 0;
            $this->repository->bulkDelete($request, function ($query) use ($request, &$unauthorized_delete_count){
                $unauthorized_delete_count = $this->model->whereIn("id", $request->ids)->where("is_user_defined", 0)->count();  
                return $query->where("is_user_defined", 1);
            });

            $message = $unauthorized_delete_count ? "Couldn't delete {$unauthorized_delete_count} items" : $this->lang('delete-success');
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($message);
    }
}
