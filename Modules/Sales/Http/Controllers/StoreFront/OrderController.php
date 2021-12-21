<?php

namespace Modules\Sales\Http\Controllers\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Transformers\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Sales\Repositories\StoreFront\OrderRepository;
use Modules\Sales\Exceptions\BankTransferNotAllowedException;
use Modules\Sales\Exceptions\CashOnDeliveryNotAllowedException;
use Modules\Sales\Exceptions\FreeShippingNotAllowedException;

class OrderController extends BaseController
{
    protected $repository;

    protected $relations = [
        "order_items.order",
        "order_taxes.order_tax_items",
        "website",
        "billing_address", 
        "shipping_address",
        "customer",
        "order_status.order_status_state",
        "order_metas"
    ];

    public function __construct(OrderRepository $repository, Order $order)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $this->model = $order;
        $this->model_name = "Order";
        $this->repository = $repository;
        $exception_statuses = [
            FreeShippingNotAllowedException::class => 403,
            BankTransferNotAllowedException::class => 403,
            CashOnDeliveryNotAllowedException::class => 403
        ];
        
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
            $fetched = $this->repository->store($request);
            $response = $this->repository->fetch($fetched->id, $this->relations);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($response), $this->lang('create-success'));
    }

    public function getShippingAndPaymentMethods(Request $request): JsonResponse
    {
        try
        {
            $method_lists = $this->repository->getMethodList($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($method_lists, $this->lang('fetch-list-success', ["name" => "Check Out"]));
    }

}
