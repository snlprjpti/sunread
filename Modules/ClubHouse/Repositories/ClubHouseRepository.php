<?php

namespace Modules\ClubHouse\Repositories;

use Exception;
use Illuminate\Support\Str;
use Modules\ClubHouse\Entities\ClubHouse;
use Modules\ClubHouse\Entities\ClubHouseValue;
use Modules\ClubHouse\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;

class ClubHouseRepository extends BaseRepository
{
    use HasScope;

    // Properties for ClubHouseRepostiory
    protected $repository, $config_fields;
    protected bool $without_pagination = true;

    /**
     * ClubHouseRepository Class Constructor
     */
    public function __construct(ClubHouse $club_house, ClubHouseValue $club_house_value)
    {
        $this->model = $club_house;
        $this->value_model = $club_house_value;
        $this->model_key = "clubhouse";

        $this->rules = [
            // ClubHouse validation
            "position" => "sometimes|nullable|integer",
            "website_id" => "required|exists:websites,id",
            "type" => "required|string|in:clubhouse,resort"
        ];

        $this->config_fields = config("clubhouse.attributes");

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
                unset($element["rules"]);

                $children_data["elements"][] = $element;
            }
            $fetched[$key] = $children_data;
        }
        return $fetched;
    }

    /**
     * Creates a Unique Slug for ClubHouse
     */
    public function createUniqueSlug(array $data, ?object $club_house = null)
    {
        $slug = isset($data["items"]["name"]["value"]) ? Str::slug($data["items"]["name"]["value"]) : $club_house->value([ "scope" => $data["scope"], "scope_id" => $data["scope_id"] ], "slug");
        $original_slug = $slug;

        $count = 1;

        while ($this->checkSlug($data, $slug, $club_house)) {
            $slug = "{$original_slug}-{$count}";
            $count++;
        }
        return $slug;
    }
}

