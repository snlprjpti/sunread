<?php

namespace Modules\Sales\Http\Controllers\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Illuminate\Database\Eloquent\Model;
use Modules\Sales\Facades\TransactionLog;
use Modules\Sales\Transformers\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Sales\Repositories\StoreFront\OrderRepository;
use Modules\Sales\Exceptions\FreeShippingNotAllowedException;

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
        $exception_statuses = [
            FreeShippingNotAllowedException::class => 403
        ];
        
        // Model::preventLazyLoading(false);

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function resource(object $order): JsonResource
    {
        return new OrderResource($order);
    }

    public function collection(object $orders): ResourceCollection
    {
        return OrderResource::collection($orders);
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $response = $this->repository->store($request);
            // $response = $this->repository->fetch($response->id, ["order_items", "order_taxes.order_tax_items"]);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($response), $this->lang('create-success'), 201);
    }

}
