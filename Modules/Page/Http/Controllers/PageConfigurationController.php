<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\PageConfiguration;
use Exception;
use Modules\Page\Repositories\PageConfigurationRepository;

class PageConfigurationController extends BaseController
{
    private $repository;

    public function __construct(PageConfiguration $pageConfiguration, PageConfigurationRepository $pageConfigurationRepository)
    {
        $this->model = $pageConfiguration;
        $this->model_name = "Page Configuration";
        $this->repository = $pageConfigurationRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function createOrUpdate(Request $request): JsonResponse
    {
        try
        {
            $scope_rules = $this->repository->scopeValidation($request);
            $data = $this->repository->validateData($request, $scope_rules, function ($current_data) {
                return $current_data->all();
            });

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

    public function getValue(Request $request): JsonResponse
    {
        try
        {
            $this->repository->validateData($request, $this->repository->scopeValidation($request));

            if(!$request->scope) $request->scope = "website";
            if(!$request->scope_id) $request->scope_id = 0;

            $fetched = $this->repository->getValues($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success'));
    }
}
