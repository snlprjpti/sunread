<?php

namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Rules\SlugUniqueRule;
use Modules\Category\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    use HasScope;

    protected $repository, $config_fields, $categoryValueRepository;
    protected bool $without_pagination = true;

    public function __construct(Category $category, CategoryValue $categoryValue, CategoryValueRepository $categoryValueRepository)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        $this->categoryValueRepository = $categoryValueRepository;
        
        $this->rules = [
            // category validation
            "position" => "sometimes|nullable|numeric",

            "parent_id" => "nullable|numeric|exists:categories,id",

            "products" => "sometimes|array",
            "products.*" => "sometimes|exists:products,id"
        ];

        $this->config_fields = config("category.attributes");

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

    public function createUniqueSlug(array $data, ?object $category = null)
    {
        $slug = isset($data["name"]["value"]) ? Str::slug($data["name"]["value"]) : $category->value([ "scope" => $data["scope"], "scope_id" => $data["scope_id"] ], "slug");
        $original_slug = $slug;

        $count = 1;

        while ($this->checkSlug($data, $slug, $category)) {
            $slug = "{$original_slug}-{$count}";
            $count++;
        }
        return $slug;
    }

    public function updatePosition(array $data, int $id): object
    {        
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $category = $this->model->findOrFail($id);
            $parent_id = isset($data["parent_id"]) ? $data["parent_id"] : null;
            
            if($parent_id) 
            {
                $parent = $this->model->findOrFail($parent_id);
                if(($id == $parent_id) || ($parent->parent_id == $id)) throw ValidationException::withMessages([ "parent_id" => "Node must not be a descendant." ]);
            }

            if($parent_id != $category->parent_id) $category->update(["parent_id" => $parent_id]);

            $all_category = $parent_id ? $parent->children : $this->model->whereParentId(null)->get();
            if($data["position"] > count($all_category)) $data["position"] = count($all_category);
            
            $allnodes = $all_category->sortBy('_lft')->values();
            $position_category = $allnodes->get(($data["position"]-1));
            $key = key(collect($allnodes)->where('id', $id)->toArray()) + 1;
            
            ($position_category->_lft < $category->_lft) ? $category->up($key-$data["position"]) : $category->down($data["position"]-$key);

        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $category);
        DB::commit();

        return $category;
    }

    public function createCategory(object $request): ?object
    {
        try
        {
            dd($request);
            $data = $this->validateData($request, array_merge($this->categoryValueRepository->getValidationRules($request), [
                "items.slug.value" => new SlugUniqueRule($request),
                "website_id" => "required|exists:websites,id"
            ]), function () use ($request) {
                return [
                    "scope" => "website",
                    "scope_id" => $request->website_id
                ];
            });
            dd($data);

            if(!isset($data["items"]["slug"]["value"])) $data["items"]["slug"]["value"] = $this->createUniqueSlug($data);

            if(isset($data["parent_id"])) if(strcmp(strval($this->model->find($data["parent_id"])->website_id), $data["website_id"]))
            throw ValidationException::withMessages(["website_id" => __("core::app.response.no_parent_belong_to_website")]);

            $created = $this->create($data, function ($created) use ($data) {
                $this->categoryValueRepository->createOrUpdate($data, $created);
                if(isset($data["channels"])) $created->channels()->sync($data["channels"]);
                if(isset($data["products"])) $created->products()->sync($data["products"]);
            });
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $created;
    }
}

