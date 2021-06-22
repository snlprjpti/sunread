<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;
use Exception;
use Modules\Page\Repositories\PageConfigurationRepository;
use Modules\Page\Transformers\PageConfigurationResource;

class PageConfigurationController extends BaseController
{
    private $repository, $page;

    public function __construct(PageConfiguration $pageConfiguration, PageConfigurationRepository $pageConfigurationRepository, Page $page)
    {
        $this->model = $pageConfiguration;
        $this->model_name = "Page Configuration";
        $this->repository = $pageConfigurationRepository;
        $this->page = $page;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return PageConfigurationResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new PageConfigurationResource($data);
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $created = $this->repository->add((object) $request) ?? [];
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
            $fetched = $this->repository->fetch($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

    public function pageDetail(Request $request)
    {
        try
        {
            $fetched = $this->model->where('page_id',$request->page_id)->where('scope',$request->scope)->where('scope_id',$request->scope_id)->first();
            if(!$fetched)
            {
                $fetched = $this->repository->getPageDetail((object) $request);
            }
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched->toArray(), $this->lang('fetch-success'));
    }
}
