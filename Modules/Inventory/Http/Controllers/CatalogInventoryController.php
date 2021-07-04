<?php

namespace Modules\Inventory\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Transformers\CatalogInventoryResource;
use Modules\Inventory\Repositories\CatalogInventoryRepository;


class CatalogInventoryController extends BaseController
{
    protected $repository;

    public function __construct(CatalogInventory $catalogInventory, CatalogInventoryRepository $catalogInventoryRepository)
    {
        $this->model = $catalogInventory;
        $this->model_name = "Catalog Inventory";
        $this->repository = $catalogInventoryRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CatalogInventoryResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CatalogInventoryResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, ["catalog_inventory_items"]);
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
            unset($data["quantity"]);
            $created = $this->repository->create($data, function($created) use($request) {
                $this->repository->syncItem($created, $request);
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
            unset($data["quantity"]);
            $updated = $this->repository->update($data, $id, function ($updated) use($request) {
                if ($request->adjustment_type) $this->repository->syncItem($updated, $request, "updated");
            });
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
