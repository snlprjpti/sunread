<?php

namespace Modules\NavigationMenu\Repositories;

use Exception;
use Illuminate\Support\Str;
use Modules\NavigationMenu\Traits\HasScope;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Entities\NavigationMenuItem;
use Modules\Core\Repositories\BaseRepository;
use Modules\NavigationMenu\Entities\NavigationMenuItemValue;

class NavigationMenuItemRepository extends BaseRepository
{
    use HasScope;

    // Properties for NavigationMenuItemRepostiory
    protected $repository, $config_fields;
    protected bool $without_pagination = true;

    /**
     * NavigationMenuItemRepostiory Class Constructor
     */
    public function __construct(NavigationMenuItem $navigationMenuItem, NavigationMenuItemValue $navigationMenuItemValue)
    {
        $this->model = $navigationMenuItem;
        $this->value_model = $navigationMenuItemValue;
        $this->model_key = "navigation_menu";

        $this->rules = [
            "navigation_menu_id" => 'required|integer|exists:navigation_menus,id',
        ];

        $this->config_fields = config("navigation_menu.attributes");

        $this->createModel();
    }

    /**
     * Get Attributes value from Config Data
     */
    public function getConfigData(array $data): array
    {
        $fetched = $this->config_fields;

        foreach($fetched as $key => $children){
            if(!isset($children["elements"])) continue;

            $children_data["title"] = $children["title"];
            $children_data["elements"] = [];

            foreach($children["elements"] as &$element){
                if($this->scopeFilter($data["scope"], $element["scope"])) continue;

                if(isset($data["navigation_menu_item_id"])){
                    $data["attribute"] = $element["slug"];

                    $existData = $this->has($data);

                    if($data["scope"] != "website") $element["use_default_value"] = $existData ? 0 : 1;
                    $elementValue = $existData ? $this->getValues($data) : $this->getDefaultValues($data);
                    $element["value"] = $elementValue?->value ?? null;
                    if ($element["type"] == "file" && $element["value"]) $element["value"] = Storage::url($element["value"]);
                }
                unset($element["rules"]);

                $children_data["elements"][] = $element;
            }
            $fetched[$key] = $children_data;
        }
        return $fetched;
    }

    /**
     * Get NavigationMenuItem with it's Attributes and Values
     */
    public function fetchWithAttributes(object $request, NavigationMenuItem $navigation_menu_item)
    {
        $data = [
            "scope" => $request->scope ?? "website",
            "scope_id" => $request->scope_id ?? $navigation_menu_item->website_id,
            "navigation_menu_item_id" => $navigation_menu_item->id
        ];

        // Accessing NavigationMenuItem title through values
        $title_data = array_merge($data, ["attribute" => "title"]);
        $navigation_menu_item->createModel();
        $value = $navigation_menu_item->has($title_data) ? $navigation_menu_item->getValues($title_data) : $navigation_menu_item->getDefaultValues($title_data);

        $fetched = [
            "id" => $navigation_menu_item->id,
            "title" => $value?->value,
        ];

        $fetched["attributes"] = $this->getConfigData($data);
        return $fetched;
    }

    /**
     * Creates a Unique Slug for NavigationMenuItem
     */
    public function createUniqueSlug(array $data, ?object $navigation_menu_item = null)
    {
        $slug = is_null($navigation_menu_item) ? Str::slug($data["items"]["title"]["value"]) : (isset($data["items"]["title"]["value"]) ? Str::slug($data["items"]["title"]["value"]) : $navigation_menu_item->value([ "scope" => $data["scope"], "scope_id" => $data["scope_id"] ], "slug"));
        $original_slug = $slug;

        $count = 1;

        while ($this->checkSlug($data, $slug, $navigation_menu_item)) {
            $slug = "{$original_slug}-{$count}";
            $count++;
        }
        return $slug;
    }

    public function checkStatus(object $navigation_menu_item, $scope): bool
    {
        try
        {
            $data = [
                "scope" => "website",
                "scope_id" => $scope->id,
            ];
            $status_value = $navigation_menu_item->value($data, "status");
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $status_value === 1 ? true : false;
    }

}

