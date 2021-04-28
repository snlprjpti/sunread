<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Currency;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Modules\Core\Transformers\CurrencyResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CurrencyController extends BaseController
{
    public function __construct(Currency $currency)
    {
        $this->model = $currency;
        $this->model_name = "Currency";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $currencies = $this->getFilteredList($request);
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

        return $this->successResponse(CurrencyResource::collection($currencies), $this->lang('fetch-list-success'));
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

            Event::dispatch('core.currency.create.before');
            $currency = $this->model->create($data);
            Event::dispatch('core.currency.create.after', $currency);
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

        return $this->successResponse(new CurrencyResource($currency), $this->lang('create-success'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $currency = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CurrencyResource($currency), $this->lang('fetch-success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {
            $data = $this->validateData($request, $id);
            $currency = $this->model->findOrFail($id);

            Event::dispatch('core.currency.update.before', $id);
            $currency = $currency->fill($data);
            $currency->save();
            Event::dispatch('core.currency.update.after', $currency);
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

        return $this->successResponse(new CurrencyResource($currency), $this->lang('update-success'));
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
            $currency = $this->model->findOrFail($id);
            // Cannot Delete if only one currency
            if ($this->model->count() == 1) return $this->errorResponse($this->lang('last-delete-error'));

            Event::dispatch('core.currency.delete.before', $currency);
            $currency->delete();
            Event::dispatch('core.currency.delete.after', $id);
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
            "code" => "required|min:3|max:3|unique:currencies,code{$id}",
            "name" => "required",
            "symbol" => "required"
        ]);
    }
}
