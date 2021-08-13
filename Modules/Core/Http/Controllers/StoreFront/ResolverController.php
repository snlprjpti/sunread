<?php

namespace Modules\Core\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\Core\Entities\Website;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Core\Repositories\ResolveRepository;

class ResolverController extends BaseController
{
    protected $repository;

    public function __construct(Website $website, ResolveRepository $resolveRepository)
    {
        $this->model = $website;
        $this->model_name = "Website";
        $this->repository = $resolveRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function resolve(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->resolveWebsiteUpdate($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-success'));
    }
}
