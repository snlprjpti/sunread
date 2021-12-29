<?php

namespace Modules\PaymentAdyen\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Adyen\Util\HmacSignature;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\PaymentAdyen\Repositories\AdyenPaymentStatusRepository;
use Modules\PaymentAdyen\Transformers\AdyenPaymentStatusUpdateResource;

class PaymentAdyenController extends BaseController
{
    protected $adyenPaymentStatusRepository;

    public function __construct(AdyenPaymentStatusRepository $adyenPaymentStatusRepository, Order $order)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');
        $this->adyenPaymentStatusRepository = $adyenPaymentStatusRepository;
        $this->model = $order;
        $this->model_name = "Order";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('paymentadyen::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('paymentadyen::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('paymentadyen::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('paymentadyen::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function resource(array $data): JsonResource
    {
        return new AdyenPaymentStatusUpdateResource($data);
    }

    public function updateOrderStatus(Request $request): JsonResponse
    {
        try
        {
            $response = $this->adyenPaymentStatusRepository->updateOrderStatus($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($response), __("core::app.response.order-status-updated"));
    }

    public function notificationWebhook(Request $request)
    {
        try
        {
            // YOUR_HMAC_KEY from the Customer Area
            $hmacKey = "6FBAE6B12B752662156C6553ED870451312A927359F675AF5019A76BC835C0B8";
            // Notification Request JSON
            $jsonRequest = "NOTIFICATION_REQUEST_JSON";
            $notificationRequest = json_decode($jsonRequest, true);
            $hmac = new HmacSignature();
            // Handling multiple notificationRequests

            dump($notificationRequest["notificationItems"]);

            foreach ( $notificationRequest["notificationItems"] as $notificationRequestItem )
            {
                $params = $notificationRequestItem["NotificationRequestItem"];
                // Handle the notification
                if ( $hmac->isValidNotificationHMAC($hmacKey, $params) )
                {
                    // Process the notification based on the eventCode
                    $eventcode = $params['eventCode'];
                    dump($eventcode);
                }
                else {
                    // Non valid NotificationRequest
                    dump('non valid notification request send');
                }
            }
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse("[accepted]", __("core::app.response.order-status-updated"));;
    }
}
