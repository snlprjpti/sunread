<?php

namespace Modules\ClubHouse\Traits;

use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Illuminate\Support\Facades\Storage;
use Modules\ClubHouse\Entities\ClubHouse;
use Modules\ClubHouse\Entities\ClubHouseValue;

trait HasScope
{
    public $channel_model, $store_model, $value_model;

    public function createModel(): void
    {
        $this->channel_model = new Channel();
        $this->store_model = new Store();
        $this->value_model = new ClubHouseValue();
    }

    public function getDefaultValues(array $data): ?object
    {
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

    public function getValues(array $data): object
    {
        return $this->checkCondition($data)->first();
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    public function has(array $data)
    {
        return (boolean) $this->checkCondition($data)->count();
    }

    public function checkCondition(array $data): object
    {
        return $this->value_model->where('club_house_id', $data["club_house_id"])->whereScope($data["scope"])->whereScopeId($data["scope_id"])->whereAttribute($data["attribute"]);
    }

    public function checkSlug(array $data, ?string $slug, ?object $club_house = null): ?object
    {
        $website_id = isset($data["website_id"]) ? $data["website_id"] : $club_house?->website_id;
        // dd($club_house->id);

        $club_house = ClubHouse::whereWebsiteId($website_id)->whereHas("values", function ($query) use ($slug, $club_house) {
            if($club_house) $query = $query->where('club_house_id', '!=', $club_house->id);
            $query->whereAttribute("slug")->whereValue($slug);
        })->first();
        return $club_house;
        // dd($club_house);
    }

    public function value(array $data, string $attribute): mixed
    {
        $this->createModel();
        $elements = collect(config("cluhbouse.attributes"))->pluck("elements")->flatten(1);
        $attribute_data = $elements->where("slug", $attribute)->first();
        $data = array_merge($data, [ "attribute" => $attribute], ["club_house_id" => $this->id]);
        $default = $this->has($data) ? $this->getValues($data) : $this->getDefaultValues($data);
        return ($attribute_data["type"] == "file" && $default?->value) ? Storage::url($default?->value) : $default?->value;
    }
}
