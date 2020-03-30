<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\CurrencyRepository;


class CurrencyController extends BaseController
{


    /**
     * CurrencyRepository object
     */
    protected $currencyRepository;

    /**
     * Create a new controller instance.
     *
     * @param CurrencyRepository $currencyRepository
     * @return void
     */
    public function __construct(CurrencyRepository $currencyRepository)
    {
        parent::__construct();
        $this->currencyRepository = $currencyRepository;
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $payload = $this->currencyRepository->paginate($this->pagination_limit);
            return $this->successResponse($payload);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     * @return JsonResponse
     */
    public function store()
    {

        try {
            Event::dispatch('core.currency.create.before');

            $this->validate(request(), [
                'code' => 'required|min:3|max:3|unique:currencies,code',
                'name' => 'required',
            ]);

            $currency = $this->currencyRepository->create(request()->all());

            Event::dispatch('core.currency.create.after', $currency);

            return $this->successResponse($currency, trans('core::app.response.create-success', ['name' => 'Currency']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update($id)
    {
        try {

            $this->validate(request(), [
                'code' => ['required', 'unique:currencies,code,' . $id],
                'name' => 'required',
            ]);

            Event::dispatch('core.currency.update.before', $id);

            $currency = $this->currencyRepository->update(request()->all(), $id);

            Event::dispatch('core.currency.update.after', $currency);

            return $this->successResponse($currency, trans('core::app.response.update-success', ['name' => 'Locale']));

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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {

            $currency = $this->currencyRepository->findOrFail($id);

            Event::dispatch('core.currency.delete.before', $currency);

            if ($this->currencyRepository->count() == 1) {
                return $this->errorResponse(trans('core::app.settings.currencies.last-delete-error'), 400);
            }

            $this->currencyRepository->delete($id);

            Event::dispatch('core.currency.delete.after', $id);

            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Currency']));

        } catch (\Exception $e) {
            return $this->errorResponse(trans('core::app.response.delete-failed', ['name' => 'Currency']));
        }

    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $currency = $this->currencyRepository->findOrFail($id);
            return $this->successResponse($currency);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }


    }
}
