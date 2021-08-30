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
    protected $repository, $page_groups, $config_fields;

    public function __construct(Category $category)
    {
        $this->model = $category;
        $this->page_groups = ["hero_banner", "usp_banner_1", "usp_banner_2", "usp_banner_3"];
        $this->config_fields = config("category.attributes");
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

    public function getPages(int $id, object $store): array
    { 
        $category = $this->model->findOrFail($id);
        $scope = [
            "scope" => "store",
            "scope_id" => $store->id
        ];
        $data = [];

        $data["id"] = $category->id;
        foreach(["name", "slug", "description"] as $key)
        {
            $data[$key] = $category->value($scope, $key);
        }

        foreach($this->page_groups as $group)
        {
            $item = [];
            $slugs = collect($this->config_fields[$group]["elements"])->pluck("slug");
            foreach($slugs as $slug)
            {
                $item[$slug] = $category->value($scope, $slug);
            }
            $data["pages"][$group] = $item;
        }

        return $data;
    }
}

