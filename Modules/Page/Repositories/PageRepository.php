<?php

namespace Modules\Page\Repositories;

use Attribute;
use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Illuminate\Validation\ValidationException;

class PageRepository extends BaseRepository
{
    protected $pageAttributeRepository;

    public function __construct(Page $page, PageAttributeRepository $pageAttributeRepository)
    {
        $this->model = $page;
        $this->model_key = "page";
        $this->rules = [
            "title" => "required",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "stores" => "required|array",
            "components" => "required|array"
        ];
        $this->pageAttributeRepository = $pageAttributeRepository;
    }

    public function validateSlug(array $data, ?int $id=null): void
    {
        $model = ($id) ? $this->model->where('id', '!=', $id) : $this->model;
        array_map(function($scope) use ($data, $model) {
            $exist_slug = $model->whereSlug($data["slug"])->whereHas("page_scopes", function ($query) use ($scope) {
                $query->whereScope("store")->whereScopeId($scope);
            })->first();
            if($exist_slug) throw ValidationException::withMessages(["slug" => "Slug has already taken."]);
        }, $data["stores"]);
    }

    public function show(int $id): array
    {
        try
        {
            $attributes = [];
            $data = $this->fetch($id, [ "page_scopes", "page_attributes" ]);
            $components = $data->page_attributes->pluck("attribute")->toArray();

            foreach($components as $component)
            {
                $values = $data->page_attributes->where("attribute", $component)->first()->toArray();
                $attributes[] = $this->pageAttributeRepository->show($component, $values["value"]);
            }

            $item = $data->toArray();
            unset($item["page_attributes"]);
            $item["components"] = $attributes;
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $item;
    }
}
