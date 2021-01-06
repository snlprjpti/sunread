<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\ExchangeRate;
use Modules\Core\Traits\ApiResponseFormat;
use Modules\Core\Transformers\ExchangeRateResource;

class ExchangeRateController extends BaseController
{
    use ApiResponseFormat;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        try {
            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';
            $exchange_rates = ExchangeRate::with(['source','target']);
            if ($request->has('q')) {
                $exchange_rates->whereLike(ExchangeRate::$SEARCHABLE, $request->get('q'));
            }
            $exchange_rates->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $exchange_rates = $exchange_rates->paginate($limit);
            return $this->successResponse($exchange_rates);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }


    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $this->validate(request(), [
                'target_currency' => ['required', 'exists:currencies,id'],
                'source_currency' => ['required', 'exists:currencies,id'],
                'rate' => 'required|numeric',
            ]);

            Event::dispatch('core.exchange_rate.create.before');

            $exchange_rate = ExchangeRate::create($request->only('source_currency', 'target_currency', 'rate'));

            Event::dispatch('core.exchange_rate.create.after', $exchange_rate);

            return $this->successResponse(new ExchangeRateResource($exchange_rate), trans('core::app.response.create-success', ['name' => 'Exchange Rate']), 200);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate(request(), [
                'target_currency' => ['required', 'exists:currencies,id'],
                'source_currency' => ['required', 'exists:currencies,id'],
                'rate' => 'required|numeric',
            ]);

            $exchange_rate = ExchangeRate::findOrFail($id);

            Event::dispatch('core.exchange_rate.update.before', $id);

            $exchange_rate = $exchange_rate->fill($request->only('source_currency', 'target_currency', 'rate'));

            $exchange_rate->save();

            Event::dispatch('core.exchange_rate.update.after', $exchange_rate);

            return $this->successResponse(new ExchangeRateResource($exchange_rate), trans('core::app.response.update-success', ['name' => 'Exchange Rate']), 200);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $exchange_rate = ExchangeRate::findOrFail($id);
            $exchange_rate->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Exchange rate']));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Get the particular resource
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $exchange_rate = ExchangeRate::findOrFail($id);
            return $this->successResponse(new ExchangeRateResource($exchange_rate));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

}
