<?php

namespace Modules\Category\Repositories;

use Modules\Category\Entities\Category;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository, $fetched = [];

    public function __construct(Category $category)
    {
        $this->model = $category;
        $this->model_key = "catalog.categories";
        $this->rules = [
            // category validation
            "name" => "required",
            "scope" => "sometimes|in:website,channel,store",
            "scope_id" => "sometimes|integer|min:1",
            "position" => "sometimes|numeric",
            "image" => "required|mimes:jpeg,jpg,bmp,png",
            "description" => "sometimes|nullable",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "status" => "sometimes|boolean",
            "include_in_menu" => "sometimes|boolean",
            "parent_id" => "nullable|numeric|exists:categories,id",
            "website_id" => "required|exists:websites,id",
        ];
    }

    public function treeWiseList(array $data, object $category, array $input = [], string $path=null): array
    { 
        $item = [
            "id" => $category->id,
            "name" => $category->values()->whereScope($data["scope"])->whereScopeId($data["scope_id"])->first()->name ?? null,
            "slug" => $category->slug
        ];

        if(count($input) > 0) 
        {
            if($path)
            {
                $keys = explode('.', $path);
                foreach($keys as $key)
                {
                    $data = $input[$key];
                }
                dd($data);
            }
            $input["children"][] = $item;
            $item = $input;
        }
        
        foreach($category->children as $children){
           dd($children->depth);
            $item = $this->treeWiseList($data, $children, $item, $path);
        } 
        return $item;
    }

}

