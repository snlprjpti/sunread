<?php

namespace Modules\Page\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Exception;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Transformers\PageResource;

class FrontPageController extends BaseController
{
    protected $repository;

    public function __construct(PageRepository $repository, Page $page)
    {
        $this->repository = $repository;
        $this->model = $page;
        $this->model_name = "Page";
        parent::__construct($this->model, $this->model_name);
    }

    public function resource(object $data): JsonResource
    {
        return new PageResource($data);
    }

    public function show(string $slug): JsonResponse
    {
        try
        {
            $page = $this->repository->findPage($slug);
            $fetch = $this->repository->fetch($page->page_id, ["page_attributes"]);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetch), $this->lang('fetch-success'));
    }
}
