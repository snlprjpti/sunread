<?php

namespace Modules\Tax\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Exception;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Tax\Entities\TaxRateTaxRule;
use Modules\Tax\Repositories\TaxRateTaxRateRepository;
use Modules\Tax\Transformers\TaxRateTaxRuleResource;

class TaxRateTaxRuleController extends BaseController
{
    protected $repository;

    public function __construct(TaxRateTaxRule $taxRateTaxRule, TaxRateTaxRateRepository $taxRateTaxRateRepository)
    {
        $this->model = $taxRateTaxRule;
        $this->repository = $taxRateTaxRateRepository;
        $this->model_name = "Tax Rate Rule";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return TaxRateTaxRuleResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new TaxRateTaxRuleResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, [ "tax_rate", "tax_rule" ]);
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
            $data = $this->repository->validateData($request);
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
            $fetched = $this->repository->fetch($id, [ "tax_rate", "tax_rule" ]);
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
            $data = $this->repository->validateData($request);
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
