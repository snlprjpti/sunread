<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Website;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\ResolveRepository;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Core\Transformers\ResolveResource;

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
                $fetched->default_channel = SiteConfig::fetch("website_default_channel", "website", $fetched->id);
                $fetched->default_store = SiteConfig::fetch("website_default_store", "website", $fetched->id);

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
