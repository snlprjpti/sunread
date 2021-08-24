<?php

namespace Modules\Category\Repositories\StoreFront;

use Exception;
use Modules\Category\Entities\Category;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Facades\CoreCache;
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

            $website = CoreCache::getWebsiteCache($request->header('hc-host'));
            $channel = CoreCache::getChannelCache($website, $request->header('hc-channel'));
            $store = CoreCache::getStoreCache($website, $channel, $request->header('hc-store'));
            $request->store = $store;

            $categories = $this->model->withDepth()->having('depth', '=', 1)->whereWebsiteId($website->id)->whereHas("values", function($query) use($store) {
                $query->whereScope("store")->whereScopeId($store->id)->whereAttribute("include_in_menu")->whereValue("1");
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

