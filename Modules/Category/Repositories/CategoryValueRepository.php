<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Traits\HasScope;

class CategoryValueRepository
{
    use HasScope;
    protected $model, $model_key, $repository, $model_name;

    public function __construct(CategoryValue $category_value, CategoryRepository $category_repository)
    {
        $this->model = $category_value;
        $this->model_key = "catalog.category.values";
        $this->repository = $category_repository;
        $this->model_name = "Category";

        $this->createModel();
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
