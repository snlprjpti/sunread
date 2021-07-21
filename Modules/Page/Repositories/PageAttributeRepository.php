<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Modules\Core\Traits\Configuration;
use Modules\Page\Entities\PageAttribute;

class PageAttributeRepository extends BaseRepository
{
    use Configuration;
    protected $config_fields, $children = [], $parent = [];

    public function __construct(PageAttribute $pageAttribute)
    {
        $this->model = $pageAttribute;
        $this->model_key = "page.attribute";
        $this->rules = [
            "position" => "sometimes|numeric",
        ];

        $this->config_fields = config("attributes");
    }

    public function updateOrCreate(array $attributes, object $parent):void
    {
        if ( !is_array($attributes) || count($attributes) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");
        try
        {
            $page_attributes = [];
            foreach($attributes as $attribute)
            {  
                $data = [
                    "value" => $attribute["value"],
                    "position" => $attribute["position"]
                ];
                $match = [
                    "page_id" => $parent->id,
                    "attribute" => $attribute["attribute"]
                ];
                $page_attributes[] = $this->model->updateOrCreate($match, $data);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $page_attributes);
        DB::commit();
    }

    public function show(string $slug)
    {
        $data = collect($this->config_fields)->where("slug", $slug)->first();
        $this->getChildren($data["attributes"]);
        return [
            "title" => $data["title"],
            "slug" => $data["slug"],
            "attributes" => $this->parent
        ];
        
    }

    public function getChildren($elements, $key = null)
    {
        if(count($this->children) > 0) $this->parent[] = $this->children;
        $this->children = [];
        if(isset($key)) $this->parent[$key]["attributes"] = [];
        foreach($elements as $i => &$element)
        {
            if($element["hasChildren"] == 0)
            {
                if( $element["provider"] !== "") $element["options"] = $this->cacheQuery((object) $element, $element["pluck"]);
                unset($element["pluck"], $element["provider"], $element["rules"]);
                if(isset($key)) $this->parent[$key]["attributes"][] = $element;
                else $this->parent[$i] = $element;
                continue;
            }
            $this->children = $element;
            $this->getChildren($element["attributes"], $i);
        }
    }
}
