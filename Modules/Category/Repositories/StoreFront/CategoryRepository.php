<?php

namespace Modules\Category\Repositories\StoreFront;

use Exception;
use Modules\Category\Entities\Category;
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

    public function getCategories(array $website): ?object
    {
        try
        {
            $categories = $this->model->whereWebsiteId($website["id"])->whereParentId(1)->whereHas("values", function($query) use($website) {
                $query->whereScope("store")->whereScopeId($website["store"]["id"])->whereAttribute("include_in_menu")->whereValue(1);
            })->get(); 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $categories;     
    }

    public function getLogo(array $website): ?string
    {
        try
        {
            $logo = (isset($website["channel"]["id"])) ? SiteConfig::fetch("logo", "website", $website["channel"]["id"]) : null; 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $logo;     
    }
}

