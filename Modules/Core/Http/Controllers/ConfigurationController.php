<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Configuration;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\Core\Rules\ConfigurationRule;
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
            $rules = isset($request->absolute_path) ? $this->getValidationRules($request->absolute_path) : ["items"=>[]];
            $rules = array_merge(["scope_id" => ["required", "integer", "min:0", new ConfigurationRule($request->scope)]], $rules);
            $data = $this->repository->validateData($request, $rules, function ($current_data) {
                return $current_data->all();
            });
            $created_data = $this->add((object) $data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($created_data->data, $this->lang($created_data->message), $created_data->code);
    }
}
