<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Currency;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Modules\Core\Transformers\CurrencyResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Repositories\CurrencyRepository;

class CurrencyController extends BaseController
{
    protected $repository;

    public function __construct(Currency $currency, CurrencyRepository $currencyRepository)
    {
        $this->model = $currency;
        $this->model_name = "Currency";
        $this->repository = $currencyRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CurrencyResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CurrencyResource($data);
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request)
    {
        try
        {
            $data = $this->repository->validateData($request);
            Event::dispatch('core.currency.create.before');

            $created = $this->repository->create($data);
            Event::dispatch('core.currency.create.after', $created);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->model->findOrFail($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "code" => "required|min:3|max:3|unique:currencies,code,{$id}"
            ]);
            Event::dispatch('core.currency.update.before', $id);

            $updated = $this->repository->update($data, $id);
            Event::dispatch('core.currency.update.after', $updated);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy($id): JsonResponse
    {
        try
        {
            // Cannot Delete if only one currency
            if ($this->model->count() == 1) return $this->errorResponse($this->lang('last-delete-error'));
            Event::dispatch('core.currency.delete.before', $id);

            $this->repository->delete($id);
            Event::dispatch('core.currency.delete.after', $id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

}
