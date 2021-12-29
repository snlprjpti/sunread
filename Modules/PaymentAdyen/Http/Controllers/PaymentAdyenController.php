<?php

namespace Modules\PaymentAdyen\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Controllers\BaseController;
use Modules\PaymentAdyen\Repositories\AdyenPaymentStatusRepository;

class PaymentAdyenController extends BaseController
{
    protected $adyenPaymentStatusRepository;

    public function __construct(AdyenPaymentStatusRepository $adyenPaymentStatusRepository)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $this->adyenPaymentStatusRepository = $adyenPaymentStatusRepository;
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

    public function updateAdyenPaymentStatus(Request $request): JsonResponse
    {
        try
        {
            $response = $this->adyenPaymentStatusRepository->updateAdyenPaymentStatus($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($response, "adyen payment status updated");

    }
}
