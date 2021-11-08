<?php

namespace Modules\Cart\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Modules\Sales\Transformers\OrderResource;
use Modules\Sales\Repositories\OrderRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;

class OrderController extends BaseController
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository, Order $order)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $this->model = $order;
        $this->model_name = "Order";
        $this->orderRepository = $orderRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function orderResource(object $order): JsonResource
    {
        return new OrderResource($order);
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $response = $this->orderRepository->store($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->orderResource($response), $this->lang('create-success'), 201);
    }

}
