<?php

namespace Modules\ClubHouse\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Rules\ScopeRule;
use Modules\ClubHouse\Entities\ClubHouse;
use Modules\ClubHouse\Rules\SlugUniqueRule;
use Illuminate\Validation\ValidationException;
use Modules\ClubHouse\Rules\ClubHouseScopeRule;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\ClubHouse\Transformers\ClubHouseResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\ClubHouse\Repositories\ClubHouseRepository;
use Modules\ClubHouse\Repositories\ClubHouseValueRepository;

class ClubHouseController extends BaseController
{
    protected $repository, $clubHouseValueRepository;

    public function __construct(ClubHouseRepository $clubHouseRepository, ClubHouse $clubHouse, ClubHouseValueRepository $clubHouseValueRepository)
    {
        $this->repository = $clubHouseRepository;
        $this->clubHouseValueRepository = $clubHouseValueRepository;

        $this->model = $clubHouse;
        $this->model_name = "ClubHouse";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return ClubHouseResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new ClubHouseResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope), new ClubHouseScopeRule($request)],
                "website_id" => "sometimes|exists:websites,id"
            ]);
            $fetched = $this->repository->fetchAll($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, array_merge($this->clubHouseValueRepository->getValidationRules($request), [
                "items.slug.value" => new SlugUniqueRule($request),
                "website_id" => "required|exists:websites,id"
            ]), function () use ($request) {
                return [
                    "scope" => "website",
                    "scope_id" => $request->website_id
                ];
            });

            if(!isset($data["items"]["slug"]["value"])) $data["items"]["slug"]["value"] = $this->repository->createUniqueSlug($data);

            $created = $this->repository->create($data, function ($created) use ($data) {
                $this->clubHouseValueRepository->createOrUpdate($data, $created);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope), new ClubHouseScopeRule($request, $id)]
            ]);

            $club_house = $this->model->findOrFail($id);
            $data = [
                "scope" => $request->scope ?? "website",
                "scope_id" => $request->scope_id ?? $club_house->website_id,
                "club_house_id" => $id
            ];

            $title_data = array_merge($data, ["attribute" => "title"]);
            // $club_house->createModel();
            // $title_value = $club_house->has($title_data) ? $club_house->getValues($title_data) : $club_house->getDefaultValues($title_data);

            $fetched = [];
            $fetched = [
                "id" => $id,
                "website_id" => $club_house->website_id,
                // "name" => $title_value->value
            ];
            $fetched["attributes"] = $this->repository->getConfigData($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            // dd($request->all());
            $club_house = $this->model->findOrFail($id);
            $data = $this->repository->validateData($request, array_merge($this->clubHouseValueRepository->getValidationRules($request, $id, "update"), [
                "items.slug.value" => new SlugUniqueRule($request, $club_house),
                "scope" => "required|in:website,channel,store",
                "scope_id" => [ "required", "integer", "min:1", new ScopeRule($request->scope), new ClubHouseScopeRule($request, $id)]
            ]), function () use ($club_house) {
                return [
                    "website_id" => $club_house->website_id
                ];
            });

            if(!isset($data["items"]["slug"]["value"]) && !isset($data["items"]["slug"]["use_default_value"])) $data["items"]["slug"]["value"] = $this->repository->createUniqueSlug($data, $club_house);

            $updated = $this->repository->update($data, $id, function ($updated) use ($data) {
                $this->clubHouseValueRepository->createOrUpdate($data, $updated);
                $updated->load("values");
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $club_house = $this->model->findOrFail($id);

            $this->repository->delete($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
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

    public function attributes(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store"
            ]);

            $fetched = $this->repository->getConfigData([
                "scope" => $request->scope ?? "website"
            ]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-success"));
    }

}
