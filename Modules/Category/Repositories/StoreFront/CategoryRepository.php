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

            $categories = $this->model->withDepth()->having('depth', '=', 0)->whereWebsiteId($website->id)->get();
            $scope = [
                "scope" => "store",
                "scope_id" => $store->id
            ]; 
            $request->sf_store = $store;

            foreach($categories as $category)
            {
                $include_value = $category->value($scope, "include_in_menu");
                if(!isset($include_value) || $include_value == "0") continue;
                $fetched["categories"][] = new CategoryResource($category);
            }

            $fetched["logo"] = SiteConfig::fetch("logo", "channel", $channel->id); 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;     
    }
}

