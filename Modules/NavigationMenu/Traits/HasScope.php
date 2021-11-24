<?php

namespace Modules\NavigationMenu\Traits;

use Exception;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Entities\NavigationMenuItem;
use Modules\NavigationMenu\Entities\NavigationMenuItemValue;

trait HasScope
{
// Proporties for HasScope Trait
    public $channel_model, $store_model, $value_model;

    /**
     * Create Model for Scope with NavigationMenuItemValue
     */
    public function createModel(): void
    {
        $this->channel_model = new Channel();
        $this->store_model = new Store();
        $this->value_model = new NavigationMenuItemValue();
    }

    /**
     * Returns Default Values for given Attributes
     */
    public function getDefaultValues(array $data): ?object
    {
        // Check for Scope if it's not `website`
        if($data["scope"] != "website")
        {
            switch($data["scope"])
            {
                case "store":
                    $data["scope"] = "channel";
                    $data["scope_id"] = $this->store_model->find($data["scope_id"])->channel->id;
                    break;

                case "channel":
                    $data["scope"] = "website";
                    $data["scope_id"] = $this->channel_model->find($data["scope_id"])->website->id;
                    break;
            }
            return $this->has($data) ? $this->getValues($data) : $this->getDefaultValues($data);
        }
        return $this->has($data) ? $this->getValues($data) : null;
    }

    /**
     * Get the Values for the Attribtues
     */
    public function getValues(array $data): object
    {
        return $this->checkCondition($data)->first();
    }

    /**
     * Filter through scope for Navigation Menu
     */
    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    // Check if the data exists
    public function has(array $data)
    {
        return (boolean) $this->checkCondition($data)->count();
    }

    // Check for condition if it exists
    public function checkCondition(array $data): object
    {
        return $this->value_model->where('navigation_menu_item_id', $data["navigation_menu_item_id"])->whereScope($data["scope"])->whereScopeId($data["scope_id"])->whereAttribute($data["attribute"]);
    }

    /**
     * Check if Slug Exists Or Not
     */
    public function checkSlug(array $data, ?string $slug, ?object $navigation_menu_item = null): ?object
    {
        $website_id = isset($data["website_id"]) ? $data["website_id"] : $navigation_menu_item?->navigationMenu->website_id;

        $navigation_menu_item = NavigationMenuItem::whereWebsiteId($website_id)->whereHas("values", function ($query) use ($slug, $navigation_menu_item) {
            if($navigation_menu_item) $query = $query->where('navigation_menu_item_id', '!=', $navigation_menu_item->id);
            $query->whereAttribute("slug")->whereValue($slug);
        })->first();
        return $navigation_menu_item;
    }

    /**
     * Return Value of the Given Data
     */
    public function value(array $data, string $attribute): mixed
    {
        $this->createModel();
        $elements = collect(config("navigation_menu.attributes"))->pluck("elements")->flatten(1);
        $attribute_data = $elements->where("slug", $attribute)->first();
        $data = array_merge($data, [ "attribute" => $attribute], ["navigation_menu_item_id" => $this->id]);
        $attribute_value = $this->has($data) ? $this->getValues($data) : $this->getDefaultValues($data);
        return ($attribute_data["type"] == "file" && $attribute_value?->value) ? Storage::url($attribute_value?->value) : $attribute_value?->value;
    }
}
