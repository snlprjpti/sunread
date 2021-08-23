<?php

namespace Modules\Category\Repositories\StoreFront;

use Exception;
use Modules\Category\Entities\Category;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\Resolver;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function getMenu(object $request): array
    {
        try
        {
            $fetched = [];

            $website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
            $channel = Channel::whereWebsiteId($website->id)->whereCode($request->header("hc-channel"))->firstOrFail();
            $store = Store::whereChannelId($channel->id)->whereCode($request->header("hc-store"))->firstOrFail();

            $categories = $this->model->withDepth()->having('depth', '=', 1)->whereWebsiteId($website->id)->whereHas("values", function($query) use($store) {
                $query->whereScope("store")->whereScopeId($store->id)->whereAttribute("include_in_menu")->whereValue(json_encode(1));
            })->get(); 
            $fetched["categories"] = CategoryResource::collection($categories);

            $fetched["logo"] = SiteConfig::fetch("logo", "website", $channel->id); 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;     
    }
}

