<?php

namespace Modules\Page\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Illuminate\Validation\ValidationException;

class PageRepository extends BaseRepository
{
    protected $config_fields;

    public function __construct(Page $page)
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
            "scopes" => "required|array",
            "attributes" => "required|array"
        ];
        $this->config_fields = config("attributes");
    }

    public function validateSlug(array $data, ?int $id=null): void
    {
        $model = ($id) ? $this->model->where('id', '!=', $id) : $this->model;
        array_map(function($scope) use ($data, $model) {
            $exist_slug = $model->whereSlug($data["slug"])->whereHas("page_scopes", function ($query) use ($scope) {
                $query->whereScope($scope["scope"])->whereScopeId($scope["scope_id"]);
            })->first();
            if($exist_slug) throw ValidationException::withMessages(["slug" => "Slug has already taken."]);
        }, $data["scopes"]);
    }
}
