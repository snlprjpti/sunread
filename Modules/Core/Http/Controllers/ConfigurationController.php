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
use Modules\Core\Transformers\ConfigurationResource;

class ConfigurationController extends BaseController
{
    protected $repository, $config_fields;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration, ConfigurationRepository $configurationRepository)
    {
        $this->model = $configuration;
        $this->model_name = "Configuration";
        $this->repository = $configurationRepository;
        $this->config_fields = config("configuration");
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
            $rules = isset($request->absolute_path) ? config('configuration.'.$request->absolute_path.'.rules') : "";
            $data = $this->repository->validateData($request, [
                "scope_id" => ["required", "integer", "min:0", new ConfigurationRule($request->scope)],
                'value' => $rules
            ]);
            $item = $this->add((object) $data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($item->data, $this->lang($item->message), $item->code);
    }
}
