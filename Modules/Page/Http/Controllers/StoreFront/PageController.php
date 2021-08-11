<?php

namespace Modules\Page\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Exception;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Transformers\StoreFront\PageResource;

class PageController extends BaseController
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

    public function show(Request $request, string $slug): JsonResponse
    {
        try
        {
            $fetched = $this->repository->findPage( $request, $slug );
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }
}
