<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryValue;

class CategoryValueRepository
{
    protected $model, $model_key, $repository;

    public function __construct(CategoryValue $category_value, CategoryRepository $category_repository)
    {
        $this->model = $category_value;
        $this->model_key = "catalog.category.values";
        $this->repository = $category_repository;
    }

    public function createOrUpdate(array $data, Model $parent): void
    {
        if ( !is_array($data) || $data == [] ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $match = [
                "category_id" => $parent->id,
                "scope" => $data["scope"],
                "scope_id" => $data["scope_id"]
            ];
            unset($data["position"], $data["website_id"], $data["parent_id"], $data["slug"]);
            $config_arrays = config("category.attributes");

            foreach($config_arrays as $config_array){
                $key = $config_array["title"];

                if($this->repository->scopeFilter($data['scope'], $config_array["scope"])) $input[$key] = $this->repository->getDefaultValues($match)->toArray()[$key];
                else{
                    $val = isset($data["attributes"][0][$key]) ? $data["attributes"][0][$key] : null;
                    if($val && isset($val["use_default_value"]) && $val["use_default_value"] != 1) throw ValidationException::withMessages([ "use_default_value" => __("core::app.response.use_default_value") ]);
                    if($val) $input[$key] = (isset($val["use_default_value"]) && $val["use_default_value"] == 1) ? $this->repository->getDefaultValues($match)->toArray()[$key] : $val["value"];
                }
            }
            $created = $this->model->updateOrCreate($match, $input);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
        DB::commit();
    }
}
