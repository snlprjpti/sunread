<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\ExchangeRate;
use Modules\Core\Transformers\ExchangeRateResource;

class ExchangeRateController extends BaseController
{
    public function __construct(ExchangeRate $exchange_rate)
    {
        $this->model = $exchange_rate;
        $this->model_name = "Exchange Rate";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $exchange_rates = $this->getFilteredList($request);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(ExchangeRateResource::collection($exchange_rates), $this->lang('fetch-list-success'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $this->validateData($request);

            Event::dispatch('core.exchange_rate.create.before');
            $exchange_rate = $this->model->create($data);
            Event::dispatch('core.exchange_rate.create.after', $exchange_rate);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ExchangeRateResource($exchange_rate), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular resource
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $exchange_rate = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ExchangeRateResource($exchange_rate), $this->lang('fetch-success'));
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
        try
        {
            $data = $this->validateData($request, $id);
            $exchange_rate = $this->model->findOrFail($id);

            Event::dispatch('core.exchange_rate.update.before', $id);
            $exchange_rate = $exchange_rate->fill($data);
            $exchange_rate->save();
            Event::dispatch('core.exchange_rate.update.after', $exchange_rate);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ExchangeRateResource($exchange_rate), $this->lang('update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $exchange_rate = $this->model->findOrFail($id);

            Event::dispatch('core.exchange_rate.delete.before', $exchange_rate);
            $exchange_rate->delete();
            Event::dispatch('core.exchange_rate.delete.after', $id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    /**
     * Custom Validation for Store/Update
     * 
     * @param Request $request
     * @param int $id
     * @return Array
     */
    private function validateData($request, $id=null)
    {
        $id = $id ? ",{$id}" : null;

        return $this->validate($request, [
            "target_currency" => "required|exists:currencies,id",
            "source_currency" => "required|exists:currencies,id",
            "rate" => "required|numeric"
        ]);
    }
}
