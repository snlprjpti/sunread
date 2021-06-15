<?php

namespace Modules\Page\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;

class PageRepository extends BaseRepository
{
    public function __construct(Page $page)
    {
        $this->model = $page;
        $this->model_key = "page";
        $this->rules = [
            // page validation
            "parent_id" => "sometimes|numeric|exists:pages,id",
            "slug" => "nullable|unique:pages,slug",
            "title" => "required",
            "description" => "required",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            // translation validation
            "translation.title" => "sometimes|required",
            "translation.description" => "sometimes|nullable",
            "translation.meta_title" => "sometimes|nullable",
            "translation.meta_description" => "sometimes|nullable",
            "translation.meta_keywords" => "sometimes|nullable",
            "translation.store_id" => "sometimes|exists:stores,id"
        ];
    }
}
