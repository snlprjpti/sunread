<?php

namespace Modules\Category\Repositories\StoreFront;

use Exception;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Exceptions\CategoryNotFoundException;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository, $config_fields, $page_groups;

    public function __construct(Category $category)
    {
        $this->model = $category;
        $this->config_fields = config("category.attributes");
        $this->page_groups = ["hero_banner", "usp_banner_1", "usp_banner_2", "usp_banner_3"];
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

    public function getCategoryData(object $request, array $category_slugs): ?array
    {
        try
        {
            $fetched = [];

            $coreCache = $this->getCoreCache($request);
            $scope = [
                "scope" => "store",
                "scope_id" => $coreCache->store->id
            ];

            $all_fetched_data = $this->getNestedcategory($coreCache, $scope, $category_slugs);
            $category = $all_fetched_data["category"];

            $fetched["category"] = $this->getCategoryDetails($category, $scope);
            $fetched["navigation"] = $this->getNavigation($category, $scope, $category_slugs);
            $fetched["breadcrumbs"] = $all_fetched_data["breadcrumbs"];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getNestedcategory(object $coreCache, array $scope, array $slugs, ?string $type = null): array
    {
        try
        {
            $parent_id = null;
            $fetched = [];
            $custom_url = [];

            if($type) {
                $count = count($slugs);
                unset($slugs[--$count]);
                if($type == "productFilter") unset($slugs[--$count]);
            }

            foreach($slugs as $slug)
            {
                $category_slug = CategoryValue::whereHas("category", function ($query) use ($parent_id) {
                    $query->whereParentId($parent_id);
                })->whereAttribute("slug")->whereValue($slug)->firstorFail();

                $category = $category_slug->category;
                $parent_id = $category_slug->category_id;

                if(isset($category_slug->scope)) {
                    if(in_array($category_slug->scope, ["channel", "website"])) $this->checkScopeForUrlKey($category_slug?->category_id, $coreCache, $category_slug?->scope, $parent_id);
                    if($category_slug->scope == "store" && $category_slug?->scope_id != $coreCache->store->id) throw new CategoryNotFoundException();
                }

                if(!$this->checkStatus($category, $scope)) throw new CategoryNotFoundException();

                if(!$type) {

                    $custom_url[] = $slug;

                    $fetched["breadcrumbs"][] = [
                        "id" => $category->id,
                        "slug" => $category->value($scope, "slug"),
                        "name" => $category->value($scope, "name"),
                        "url" => implode("/", $custom_url)
                    ];
                }
                $fetched["category"] = $category;
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getCategoryDetails(object $category, array $scope): array
    {
        try
        {
            $data = [];

            $data["id"] = $category->id;
            foreach(["name", "slug", "description", "layout_type", "categories", "no_of_items", "pagination"] as $key) $data[$key] = $category->value($scope, $key);
            foreach(["meta_title", "meta_keywords", "meta_description"] as $key) $data["seo"][$key] = $category->value($scope, $key);

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
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function getNavigation(object $category, array $scope, array $slugs): array
    {
        try
        {
            $count = count($slugs);
            $categories = $category->children;

            $fetched = [];
            foreach($categories as $single_category)
            {
                if(!$this->checkMenuStatus($category, $scope)) continue;
                if(!$this->checkStatus($category, $scope)) continue;

                $slug =  $single_category->value($scope, "slug");
                $slugs[++$count] = $slug;

                $fetched[] = [
                    "id" => $single_category->id,
                    "slug" => $slug,
                    "name" => $single_category->value($scope, "name"),
                    "url" => implode("/", $slugs)
                ];
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }

    public function checkScopeForUrlKey(?int $category_id, object $coreCache, ?string $custom_scope, ?int $parent_id): void
    {
        try
        {
            if($custom_scope == "channel") {
                $scope_product_attr = CategoryValue::whereHas("category", function ($query) use ($parent_id) {
                    $query->whereParentId($parent_id);
                })->whereAttribute("slug")->whereCategoryId($category_id)->whereScope("store")->whereScopeId($coreCache->store->id)->first();
                if($scope_product_attr) throw new CategoryNotFoundException();
            }
            if($custom_scope == "website") {
                $scope_product_attr = CategoryValue::whereHas("category", function ($query) use ($parent_id) {
                    $query->whereParentId($parent_id);
                })->whereAttribute("slug")->whereCategoryId($category_id)->whereScope("channel")->whereScopeId($coreCache->channel->id)->first();
                if($scope_product_attr) throw new CategoryNotFoundException();
                else $this->checkScopeForUrlKey($category_id, $coreCache, "channel", $parent_id);
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }
}

