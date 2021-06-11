<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageAvailability;
use Modules\Page\Exceptions\AlreadyCreatedException;
use Modules\Page\Repositories\PageAvailabilityRepository;
use Exception;
use Modules\Page\Transformers\PageAvailabilityResource;

class PageAvailabilityController extends BaseController
{
    private $page, $repository;

    public function __construct(PageAvailability $pageAvailability, PageAvailabilityRepository $pageAvailabilityRepository, Page $page)
    {
        $this->model = $pageAvailability;
        $this->model_name = "Page Availability";
        $this->page = $page;
        $this->repository = $pageAvailabilityRepository;
        $exception_statuses = [
            AlreadyCreatedException::class => 409
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function collection(object $data): ResourceCollection
    {
        return PageAvailabilityResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new PageAvailabilityResource($data);
    }

    public function allowPage(Request $request, int $page_id): JsonResponse
    {
        try
        {
            $page = $this->page->whereId($page_id)->activePage(1)->firstOrFail();

            $data = $this->repository->getBulkData($request, $page);
            $this->repository->insertBulkData($data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('create-success'), 201);
    }

    public function deleteAllowPage(Request $request): JsonResponse
    {
        try
        {
            $this->repository->bulkDelete($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

    public function modelList(): JsonResponse
    {
        try
        {
            $fetched = config('model_list.model_types');
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success', ["name" => "Model List"]));
    }
}
