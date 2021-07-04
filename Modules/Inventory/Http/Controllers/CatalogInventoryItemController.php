<?php

namespace Modules\Inventory\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Inventory\Entities\CatalogInventoryItem;
use Modules\Inventory\Transformers\CatalogInventoryItemResource;
use Modules\Inventory\Repositories\CatalogInventoryItemRepository;

class CatalogInventoryItemController extends BaseController
{
    protected $repository;

    public function __construct( CatalogInventoryItem $catalogInventoryItem, CatalogInventoryItemRepository $catalogInventoryItemRepository )
    {
        $this->model = $catalogInventoryItem;
        $this->model_name = "Catalog Inventory Item";
        $this->repository = $catalogInventoryItemRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CatalogInventoryItemResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CatalogInventoryItemResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
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

            $data = $this->repository->validateData($request);
            $created = $this->repository->create($data, function($created) use($request){
                $created->catalog_inventories()->sync($request->get("catalog_inventories"));
                $this->repository->adjustment($created, $request);
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang("fetch-success"));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $updated = $this->repository->update($data, $id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function destroy(int $id): jsonResponse
    {
        try
        {
            $this->repository->delete($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang("delete-success"), 204);
    }
}
