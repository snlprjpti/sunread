<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Website;
use Modules\Core\Transformers\ResolveResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\ResolveRepository;
use Modules\Core\Http\Controllers\BaseController;

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

    public function resource(object $data): JsonResource
    {
        return new ResolveResource($data);
    }

    public function resolve(?string $website = null): JsonResponse
    {
        try
        {
            $fetched = $this->repository->resolveWebsite($website, function ($fetched) {
                $fetched->config = SiteConfig::fetch("default_country", "website", $fetched->id);
                return $fetched;
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }
}
