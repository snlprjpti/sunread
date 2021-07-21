<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Exception;
use Modules\Page\Entities\PageAttribute;
use Modules\Page\Repositories\PageAttributeRepository;

class PageAttributeController extends BaseController
{
    protected $repository;

    public function __construct(PageAttribute $pageAttribute, PageAttributeRepository $pageAttributeRepository)
    {
        $this->model = $pageAttribute;
        $this->model_name = "Page Attribute";
        $this->repository = $pageAttributeRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            foreach($this->config_fields as $field)
            {
                unset($field["attributes"]);
                $component[] = $field;
            }
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($component, $this->lang('fetch-list-success'));
    }

    public function show(string $slug): JsonResponse
    {
        try
        {
            $fetched = $this->repository->show($slug);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success'));
    }
}
