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
            $fetched = $this->config_fields;
            $checkKey = [ 'scope' => $request->scope, 'scope_id' => $request->scope_id ];
            foreach($fetched as $key => $data)
            {
                if(!isset($data['children'])) continue;
                foreach($data['children'] as $i => $children)
                {
                    if(!isset($children['subChildren'])) continue;
                    foreach($children['subChildren'] as $j => &$subchildren)
                    {
                        if(!isset($subchildren['elements'])) continue;
                        foreach($subchildren['elements'] as $k => &$element)
                        {
                            if(!in_array($request->scope, $element['showIn']))
                            {
                                unset($subchildren['elements'][$k]);
                                continue;
                            }
                            $checkKey["path"] = $element['path'];
                            $element['default'] = $this->has((object) $checkKey) ? $this->getDefaultValues((object) $checkKey) : "" ;
                            $element['value'] = $this->cacheQuery((object) $checkKey, $element['pluck']);
                            $element['absolute_path'] = $key.'.children.'.$i.'.subChildren.'.$j.'.elements.'.$k;
                            $subchildren['elements'][$k] = $element;
                            
                        }
                        $children['subChildren'][$j] = $subchildren;
                    }
                    $data['children'][$i] = $children;
                }
                $fetched[$key] = $data;
            }
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
            $rules = config('configuration.'.$request->absolute_path.'.rules');
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

        return $this->successResponse($item->data, $this->lang($item->message), 201);
    }
}
