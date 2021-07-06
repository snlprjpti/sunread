<?php

namespace Modules\Inventory\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Inventory\Transformers\CatalogInventoryResource;
use Modules\Inventory\Repositories\CatalogInventoryRepository;


class CatalogInventoryController extends BaseController
{
    protected $repository, $model_key;

    public function __construct(CatalogInventory $catalogInventory, CatalogInventoryRepository $catalogInventoryRepository)
    {
        $this->model = $catalogInventory;
        $this->model_name = "Catalog Inventory";
        $this->model_key = "catalog.inventories";
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
            $fetched = $this->repository->fetchAll($request, [ "product", "website" ]);
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
            $created = $this->repository->create($data, function (&$created) use ($request) {
                LogCatalogInventoryItem::dispatchSync([
                    "product_id" => $created->product_id,
                    "website_id" => $created->website_id,
                    "event" => "{$this->model_key}.store",
                    "adjustment_type" => "addition",
                    "adjusted_by" => auth()->guard("admin")->id(),
                    "quantity" => $request->quantity
                ]);
                $created->quantity = $request->quantity;
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
            $fetched = $this->repository->fetch($id, [ "catalog_inventory_items.admin", "product", "website" ]);
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

            $updated = $this->repository->update($data, $id, function (&$updated) use ($request) {
                $original_quantity = (float) $updated->quantity;
                $adjustment_type = (($request->quantity - $original_quantity) > 0) ? "addition" : "deduction";
                LogCatalogInventoryItem::dispatchSync([
                    "product_id" => $updated->product_id,
                    "website_id" => $updated->website_id,
                    "event" => "{$this->model_key}.update",
                    "adjustment_type" => $adjustment_type,
                    "adjusted_by" => auth()->guard("admin")->id(),
                    "quantity" => (float) abs($original_quantity - $request->quantity)
                ]);
                $updated->quantity = $request->quantity;
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

        return $this->successResponseWithMessage($this->lang("delete-success"));
    }
}
