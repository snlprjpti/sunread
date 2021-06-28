<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
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
            foreach($data["attributes"][0] as $key => $attribute){
                // if($this->repository->scopeFilter($data['scope'], $configDataArray["scope"])) continue;
                $input[$key] = ($data["scope"] != "website" && $attribute["use_in_default"] == 1) ? $this->repository->scopeFilter($data) : $attribute["value"];
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
