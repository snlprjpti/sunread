<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Configuration;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationController extends BaseController
{
    protected $repository;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration, ConfigurationRepository $configurationRepository)
    {
        $this->model = $configuration;
        $this->model_name = "Configuration";
        $this->repository = $configurationRepository;
        $this->createModel();

        parent::__construct($this->model, $this->model_name);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->getConfigData($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $rules = isset($request->absolute_path) ? $this->repository->getValidationRules($request) : ["items"=>[]];
            $scope_rules = $this->repository->scopeValidation($request);
            $rules = array_merge($scope_rules, $rules);
            $data = $this->repository->validateData($request, $rules);

            if(!$request->scope) $data["scope"] = "global";
            if(!$request->scope_id) $data["scope_id"] = 0;

            $created_data = $this->repository->add((object) $data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($created_data->data ?? [], $this->lang($created_data->message), $created_data->code);
    }

    public function getElementValue(Request $request)
    {
        try
        {
            $this->repository->validateData($request, $this->repository->scopeValidation($request));

            if(!$request->scope) $request->scope = "global";
            if(!$request->scope_id) $request->scope_id = 0;

            $fetched = ($this->has((object) $request)) ? $this->repository->getValues($request) : $this->repository->getDefaultValues($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success'));
    }
}
