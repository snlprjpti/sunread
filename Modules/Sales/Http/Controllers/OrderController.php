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
use Modules\Sales\Repositories\StoreFront\OrderRepository;
use Modules\Sales\Repositories\OrderStatusUpdateRepository;

class OrderController extends BaseController
{
    protected $repository, $orderStatusUpdateRepository;

    public function __construct(OrderRepository $repository, Order $order, OrderStatusUpdateRepository $orderStatusUpdateRepository)
    {
        $this->model = $order;
        $this->model_name = "Order";
        $this->repository = $repository;
        $this->orderStatusUpdateRepository = $orderStatusUpdateRepository;

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
            $fetched = $this->repository->fetchAll($request, ["order_items.order", "order_taxes.order_tax_items", "website", "billing_address", "shipping_address", "customer"]);
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
            $fetched = $this->repository->fetch($id, ["order_items.order", "order_taxes.order_tax_items", "website", "billing_address", "shipping_address", "customer"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function orderStatus(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->orderStatusUpdateRepository->orderStatus($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang('update-success'));     
    }

}
