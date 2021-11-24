<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Sales\Entities\OrderStatus;
use Modules\Sales\Repositories\OrderStatusRepository;
use Modules\Sales\Transformers\OrderStatusResource;

class OrderStatusController extends BaseController
{
    protected $repository;

    public function __construct(OrderStatus $order_status, OrderStatusRepository $repository)
    {
        $this->model = $order_status;
        $this->model_name = "OrderStatus";
        $this->repository = $repository;
        parent::__construct($this->model, $this->model_name);
    }

    public function resource(object $order): JsonResource
    {
        return new OrderStatusResource($order);
    }

    public function collection(object $orders): ResourceCollection
    {
        return OrderStatusResource::collection($orders);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, ["order_status_state"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "name" => "unique:order_statuses,name"
            ]); 
            $data["slug"] = $this->model->createSlug($request->name);
            $created = $this->repository->create($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($id, ["order_status_state"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang("fetch-success"));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request,[
                "name" => "unique:order_statuses,name,{$id}",
            ]);
            $updated = $this->repository->update($data, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }
}
