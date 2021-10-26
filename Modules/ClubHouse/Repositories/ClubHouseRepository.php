<?php

namespace Modules\Clubhouse\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\ClubHouse\Entities\ClubHouse;
use Modules\ClubHouse\Entities\ClubHouseValue;
use Modules\ClubHouse\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;

class ClubhouseRepository extends BaseRepository
{
    use HasScope;

    protected $repository, $config_fields;
    protected bool $without_pagination = true;

    public function __construct(ClubHouse $clubHouse, ClubHouseValue $clubHouseValue)
    {
        $this->model = $clubHouse;
        $this->value_model = $clubHouseValue;
        $this->model_key = "clubhouse";

        $this->rules = [
            // ClubHouse validation
            "position" => "sometimes|nullable|numeric",
        ];

        $this->config_fields = config("clubhouse.attributes");

        $this->createModel();
    }

    public function getConfigData(array $data): array
    {
        $fetched = $this->config_fields;

        foreach($fetched as $key => $children){
            if(!isset($children["elements"])) continue;

            $children_data["title"] = $children["title"];
            $children_data["elements"] = [];

            foreach($children["elements"] as &$element){
                if($this->scopeFilter($data["scope"], $element["scope"])) continue;

                if(isset($data["category_id"])){
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

    public function createUniqueSlug(array $data, ?object $clubHouse = null)
    {
        $slug = isset($data["items"]["name"]["value"]) ? Str::slug($data["items"]["name"]["value"]) : $clubHouse->value([ "scope" => $data["scope"], "scope_id" => $data["scope_id"] ], "slug");
        $original_slug = $slug;

        $count = 1;

        while ($this->checkSlug($data, $slug, $clubHouse)) {
            $slug = "{$original_slug}-{$count}";
            $count++;
        }
        return $slug;
    }
}

