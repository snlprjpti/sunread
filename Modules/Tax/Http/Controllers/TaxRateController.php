<?php

namespace Modules\Tax\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Tax\Entities\TaxRate;
use Modules\Tax\Transformers\TaxRateResource;
use Modules\Tax\Repositories\TaxRateRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;
use Modules\Country\Entities\Region;

class TaxRateController extends BaseController
{
    protected $repository;

    public function __construct(TaxRate $taxRate, TaxRateRepository $taxRateRepository)
    {
        $this->repository = $taxRateRepository;
        $this->model = $taxRate;
        $this->model_name = "Tax Rate";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return TaxRateResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new TaxRateResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, callback:function () use($request) {
                $region = Region::whereId($request->region_id)->first();
                if ( $region->country_id !== $region->country_id) throw new ValidationException(["message" => "Country not found on region."]); 
            });    
            $created = $this->repository->create($data);
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
            $fetched = $this->repository->fetch($id, ["country", "region"]);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "identifier" => "required|unique:tax_rates,identifier,{$id}"
            ], function () use ($request) {
                $region = Region::whereId($request->region_id)->first();
                if ( $region->country_id !== $region->country_id) throw new ValidationException(["message" => "Country not found on region."]); 
            });
            $updated = $this->repository->update($data, $id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
