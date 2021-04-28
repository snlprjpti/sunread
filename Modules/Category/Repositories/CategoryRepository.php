<?php

namespace Modules\Category\Repositories;

use Modules\Category\Entities\Category;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository;

    public function __construct(Category $category)
    {
        $this->model = $category;
        $this->model_key = "catalog.categories";
        $this->rules = [
            // category validation
            "name" => "required",
            "slug" => "nullable|unique:categories,slug",
            "position" => "sometimes|numeric",
            "image" => "required|mimes:jpeg,jpg,bmp,png",
            "description" => "sometimes|nullable",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "status" => "sometimes|boolean",
            "parent_id" => "sometimes|exists:categories,id",
            // translation validation
            "translation.name" => "sometimes|required",
            "translation.description" => "sometimes|nullable",
            "translation.meta_title" => "sometimes|nullable",
            "translation.meta_description" => "sometimes|nullable",
            "translation.meta_keywords" => "sometimes|nullable",
            "translation.store_id" => "required|exists:stores,id"
        ];
    }

}
