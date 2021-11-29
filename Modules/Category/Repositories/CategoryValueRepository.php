<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Traits\HasScope;

class CategoryValueRepository
{
    use HasScope;
    protected $model, $model_key, $repository, $model_name, $global_file = [];

    public function __construct(CategoryValue $category_value, CategoryRepository $category_repository)
    {
        $this->model = $category_value;
        $this->model_key = "catalog.category.values";
        $this->repository = $category_repository;
        $this->model_name = "Category";

        $this->createModel();
    }

    public function getValidationRules(object $request, ?int $id = null, ?string $method = null): array
    {
        try
        {
            $this->global_file = [];
            $scope = $request->scope ?? "website";
            $all_rules = collect(config("category.attributes"))->pluck("elements")->flatten(1)->map(function($data) {
                return $data;
            })->reject(function ($data) use ($scope) {
                return $this->scopeFilter($scope, $data["scope"]);
            })->mapWithKeys(function ($item) use ($scope, $id, $method, $request) {

                $prefix = "items.{$item["slug"]}";
                $value_path = "{$prefix}.value";
                $default_path = "{$prefix}.use_default_value";

                $value_rule = ($item["is_required"] == 1) ? (($scope != "website") ? "required_without:{$default_path}" : "required") : "nullable";
                $value_rule = "$value_rule|{$item["rules"]}";
                if($scope != "website") $default_rule = ($item["is_required"] == 1) ? "required_without:{$value_path}|{$item["rules"]}" : "boolean";

                if ($method == "update" && $id && $item["type"] == "file") $value_rule = $this->handleFileIssue($id, $request, $item, $value_rule);

                $rules = [
                    $value_path => $value_rule
                ];
                return isset($default_rule) ? array_merge($rules, [ $default_path => $default_rule ]) : $rules;
            })->toArray();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $all_rules;
    }

    public function handleFileIssue(int $id, object $request, array $item, ?string $value_rule): ?string
    {
        try
        {
            $exist_category = Category::findOrFail($id);

            if (isset($request->items[$item["slug"]])) {
                $request_slug = $request->items[$item["slug"]];
                if (isset($request_slug["value"]) && !is_file($request_slug["value"])  && !isset($request_slug["use_default_value"])) {
                $exist_file = $exist_category->values()->whereAttribute($item["slug"])->whereScope($request->scope ?? "website")->whereScopeId($request->scope_id ?? $exist_category->website_id)->first();
                    if ($exist_file?->value && (Storage::url($exist_file?->value) == $request_slug["value"])) {
                        $this->global_file[] = $item["slug"];
                        return "";
                    }
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $value_rule;
    }

    public function createOrUpdate(array $data, Model $parent): void
    {
        if ( !is_array($data) || $data == [] ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created_data = [];
            $match = [
                "category_id" => $parent->id,
                "scope" => $data["scope"],
                "scope_id" => $data["scope_id"]
            ];

            foreach($data["items"] as $key => $val)
            {
                if(in_array($key, $this->global_file)) continue;

                if(isset($val["use_default_value"]) && $val["use_default_value"] != 1) throw ValidationException::withMessages([ "use_default_value" => __("core::app.response.use_default_value") ]);

                if(!isset($val["use_default_value"]) && !array_key_exists("value", $val)) throw ValidationException::withMessages([ "value" => __("core::app.response.value_missing", ["name" => $key]) ]);

                $absolute_path = config("category.absolute_path.{$key}");
                $configDataArray = config("category.attributes.{$absolute_path}");

                if($this->scopeFilter($match["scope"], $configDataArray["scope"])) continue;

                $match["attribute"] = $key;

                $value = $val["value"] ?? null;
                $match["value"] = ($configDataArray["type"] == "file" && $value) ? $this->repository->storeScopeImage($value, "category") : $value;

                if($configData = $this->checkCondition($match)->first())
                {
                    if(isset($val["use_default_value"])  && $val["use_default_value"] == 1) $configData->delete();
                    else $created_data["data"][] = $configData->update($match);
                    continue;
                }
                if(isset($val["use_default_value"])  && $val["use_default_value"] == 1) continue;
                $created_data["data"][] = $this->model->create($match);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created_data);
        DB::commit();
    }
}
