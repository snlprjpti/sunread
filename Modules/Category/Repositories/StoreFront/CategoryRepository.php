<?php

namespace Modules\Category\Repositories\StoreFront;

use Exception;
use Modules\Category\Entities\Category;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function checkMenuStatus(object $category, array $scope): bool
    {
        try
        {
            $include_value = $category->value($scope, "include_in_menu");
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return (isset($include_value) && $include_value == "1");
    }

    public function checkStatus(object $category, array $scope): bool
    {
        try
        {
            $status_value = $category->value($scope, "status");
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return (isset($status_value) && $status_value == "1");
    }

    public function getMenu(object $request): array
    {
        try
        {
            $fetched = [];
            $coreCache = $this->getCoreCache($request);

            $categories = $this->model->withDepth()->having('depth', '=', 0)->whereWebsiteId($coreCache->website->id)->get();
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ];

            $fetched["categories"] = $this->getCategories($categories, $scope);

            $fetched["logo"] = SiteConfig::fetch("logo", "channel", $coreCache->channel->id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getCategories(object $categories, array $scope): array
    {
        try
        {
            $fetched = [];
            foreach($categories as $category)
            {
                if(!$this->checkMenuStatus($category, $scope)) continue;
                if(!$this->checkStatus($category, $scope)) continue;
                $fetched[] = new CategoryResource($category);
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }
}

