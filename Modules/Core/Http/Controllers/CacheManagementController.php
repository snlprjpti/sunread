<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\CacheManagement;
use Modules\Core\Repositories\CacheManagementRepository;
use Modules\Core\Transformers\CacheManagementResource;

class CacheManagementController extends BaseController
{
    private $repository;

    public function __construct(CacheManagement $cacheManagement, CacheManagementRepository $cacheManagementRepository)
    {
        $this->model = $cacheManagement;
        $this->model_name = "Cache Management";

        parent::__construct($this->model, $this->model_name);
        $this->repository = $cacheManagementRepository;
    }

    public function collection(object $data): ResourceCollection
    {
        return CacheManagementResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CacheManagementResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function clearCache(Request $request): JsonResponse
    {
        try
        {
           $this->repository->clearCustomCache($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang("response.clear-success", [ "name" => "Cache" ]));
    }

    public function clearAllCache(): JsonResponse
    {
        try
        {
            if (count(Redis::keys("*")) > 0) Redis::del(Redis::keys("*"));
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang("response.clear-success", [ "name" => "All Cache" ]));
    }
}
