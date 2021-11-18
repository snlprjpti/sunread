<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Modules\Sales\Transformers\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Sales\Repositories\OrderStatusRepository;
use Modules\Sales\Repositories\StoreFront\OrderRepository;

class OrderController extends BaseController
{
    protected $repository, $orderStatusRepository;

    public function __construct(OrderRepository $repository, Order $order, OrderStatusRepository $orderStatusRepository)
    {
        $this->model = $order;
        $this->model_name = "Order";
        $this->repository = $repository;
        $this->orderStatusRepository = $orderStatusRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function resource(object $order): JsonResource
    {
        return new OrderResource($order);
    }

    public function collection(object $orders): ResourceCollection
    {
        return OrderResource::collection($orders);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, ["order_items", "order_taxes.order_tax_items"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($id, ["order_items", "order_taxes.order_tax_items"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function orderStatus(Request $request, int $order_id): JsonResponse
    {
        try
        {
            $this->orderStatusRepository->orderStatus($request, $order_id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponseWithMessage($this->lang('update-success'), 201);       
    }

}
