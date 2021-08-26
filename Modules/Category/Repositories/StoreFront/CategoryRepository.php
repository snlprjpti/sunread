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

