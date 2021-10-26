<?php

namespace Modules\Page\Repositories\StoreFront;

use Exception;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;
use Modules\Product\Transformers\StoreFront\ProductResource;

class PageRepository extends BaseRepository
{
    public $config_fields;

    public function __construct(Page $page)
    {
        $this->model = $page;
        $this->model_key = "page";
        $this->without_pagination = true;
        $this->config_fields = config("attributes");
    }

    public function findPage(object $request, string $slug): array
    {
        try
        {     
            $fetched = []; 
            $coreCache = $this->getCoreCache($request);

            $page = $this->model->with("page_attributes")->whereWebsiteId($coreCache->website->id)->whereSlug($slug)->firstOrFail();
            $page_scope = $page->page_scopes()->whereScope("store");
            $all_scope = (clone $page_scope)->whereScopeId(0)->first();
            if (!$all_scope) $page_scope->whereScopeId($coreCache->store->id)->firstOrFail();
            $fetched = $page->toArray();
            $fetched["components"] = $this->getComponent($coreCache->store, $page->page_attributes);
            unset($fetched["page_attributes"], $fetched["created_at"], $fetched["updated_at"]);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getComponent(object $store, object $components): array
    {
        try
        {
            $all_components = [];
            foreach($components as $component)
            {
                $data["component"] = $component->attribute;
                $data["attributes"] = $this->getElements($store, $component->attribute, $component->value);
                $all_components[] = $data;
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $all_components;
    }

    public function getElements(object $store, string $component, array $attributes): array
    {
        try
        {
            $collect_elements = [];
            $group_elements = collect($this->config_fields)->where("slug", $component)->pluck("mainGroups")->flatten(1);
            foreach($group_elements as $group_element)
            {
                if($group_element["type"] == "module") {
                    foreach($group_element["subGroups"] as $module)
                    {
                        $collect_elements[] = $module["groups"];
                    }
                    continue;
                }
                $collect_elements[] = $group_element["groups"];
            }
            $collect_elements = collect($collect_elements)->flatten(1)->pluck("attributes")->flatten(1);
            
            foreach($attributes as $field => $attribute)
            {
                $selected_element = $collect_elements->where("slug", $field)->first();
                if (isset($selected_element["provider"]) && $selected_element["provider"] != "") $attributes[$field] = $this->getProviderData($store, $selected_element, $attribute);
                if ($selected_element["type"] == "file") $attributes[$field] = Storage::url($attribute);
                if ($selected_element["type"] == "repeater") $attributes[$field] = $this->getRepeatorType($store, $selected_element, $attribute);
                if ($selected_element["type"] == "normal") $attributes[$field] = $this->getNormalType($store, $selected_element, $attribute);

                if ($selected_element["slug"] == "dynamic_link" && $attribute == "0") $attributes["view_more_link"] = "";
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $attributes;
    }

    private function getRepeatorType(object $store, array $repeators, array $attributes)
    {
        try
        {
            $repeator_elements = collect($repeators["attributes"])->flatten(1);
            foreach($attributes as $key => $attribute)
            {
                foreach($attribute as $field => $att)
                {
                    $selected_element = $repeator_elements->where("slug", $field)->first();
                    if ($selected_element["provider"] != "") $attributes[$key][$field] = $this->getProviderData($store, $selected_element, $attribute);
                    if ($selected_element["type"] == "file") $attributes[$key][$field] = Storage::url($att); 
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $attributes;
    }

    private function getNormalType(object $store, array $normals, array $attributes)
    {
        try
        {
            $normal_elements = collect($normals["attributes"])->flatten(1);
            foreach($attributes as $field => $attribute)
            {
                $selected_element = $normal_elements->where("slug", $field)->first();
                if ($selected_element["provider"] != "") $attributes[$field] = $this->getProviderData($store, $selected_element, $attribute);
                if ($selected_element["type"] == "file") $attributes[$field] = Storage::url($attribute); 
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $attributes;
    }

    public function getProviderData(object $store, array $element, mixed $values): mixed
    {
        try
        {
            $model = new $element["provider"];
            $pluck = $element["pluck"][1];
            $fetched = $model->where($pluck, $values)->first();

            if($element["slug"] == "products_repeater_sku") {
                $is_visibility = $fetched->value([
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_slug" => "visibility"
                ]);
                
                $fetched = ($is_visibility?->name != "Not Visible Individually") ? new ProductResource($fetched) : [];
            } 
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;   
    }

}
