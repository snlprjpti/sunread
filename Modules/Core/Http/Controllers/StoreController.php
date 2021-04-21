<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Core\Entities\Store;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Transformers\StoreResource;

class StoreController extends BaseController
{

    public function __construct(Store $store)
    {
        $this->model = $store;
        $this->model_name = "Store";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        try{
            $this->validateListFiltering($request);
            $stores = $this->getFilteredList($request);
        }
        catch( QueryException $exception )
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch( ValidationException $exception )
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch( Exception $exception )
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(StoreResource::collection($stores), $this->lang("fetch-list-success"));

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        try{

            $data = $this->validateData($request);

            Event::dispatch('core.store.create.before');
            $store = $this->model->create($data);
            Event::dispatch('core.store.create.after', $store);

        }
        catch(QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch( ValidationException $exception )
        {
            return $this->errorResponse($exception->getMessage(), 422);
        }
        catch(\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new StoreResource($store), $this->lang('create-success'),201);

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try{
            $store = $this->model->findOrFail($id);
        }
        catch(\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }
        return $this->successResponse(new StoreResource($store), $this->lang('fetch-success'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateData($request);
            $store = $this->model->findOrFail($id);

            Event::dispatch("core.store.update.before", $id);
            $store = $store->fill($data);
            $store->save();
            Event::dispatch("core.store.update.after", $store);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new StoreResource($store), $this->lang("update-success"));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $store = $this->model->findOrFail($id);

            Event::dispatch("core.store.delete.before", $store);
            $store->delete();
            Event::dispatch("core.store.delete.after", $id);
        }
        catch (\Exception $exception)
        {

        }
    }

    private function validateData($request, $id = null)
    {
        $id = $id ? ",{$id}" : null;
        $mimes = "bmp,jpeg,jpg,png,webp";
        $sometimes = $id ? "sometimes" : "required";

        return $this->validate($request,[
            "currency" => "required",
            "name" => "required",
            "locale" => "required",
            "image" => "{$sometimes}|mimes:{$mimes}"
        ]);
    }


}
