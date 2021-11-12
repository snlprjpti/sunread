<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Modules\Sales\Transformers\OrderResource;
use Modules\Sales\Repositories\OrderRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Sales\Facades\TransactionLog;

class OrderController extends BaseController
{
    protected $repository;

    public function __construct(OrderRepository $repository, Order $order)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $this->model = $order;
        $this->model_name = "Order";
        $this->repository = $repository;

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
            $fetched = $this->repository->fetchAll($request, ["order_items"], function () use ($request)   {
                return $this->model->where("website_id", $request->website_id);
            });
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
            $fetched = $this->repository->fetch($id, ["order_items"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $response = $this->repository->store($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($response), $this->lang('create-success'), 201);
    }

}
