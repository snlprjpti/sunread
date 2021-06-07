<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageAvailabilty;
use Modules\Page\Repositories\PageAvailabiltyRepository;
use Exception;
use Modules\Page\Transformers\PageAvailabiltyResource;

class PageAvailabilityController extends BaseController
{
    private $page;
    private $repository;

    protected function __construct(PageAvailabilty $pageAvailabilty, PageAvailabiltyRepository $pageAvailabiltyRepository, Page $page)
    {
        $this->model = $pageAvailabilty;
        $this->model_name = "Page Availability";
        $this->page = $page;
        $this->repository = $pageAvailabiltyRepository;

        $exception_statuses = [
            AlreadyCreatedException::class => 400
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }


    public function collection(object $data): ResourceCollection
    {
        return PageAvailabiltyResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new PageAvailabiltyResource($data);
    }

    public function allowPage(Request $request, int $page_id): JsonResponse
    {
        try
        {
            // Get requested page with Status 1
            $coupon = $this->page->whereId($page_id)->whereStatus(1)->firstOrFail();

            $data = $this->repository->getBulkData($request, $coupon);
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
            $request->validate([
                'ids' => 'array|required',
                'ids.*' => 'required|exists:allow_coupons,id',
            ]);

            $deleted = $this->model->whereIn('id', $request->ids);
            $deleted->delete();
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }
}
