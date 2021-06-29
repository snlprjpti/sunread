<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryValue;

class CategoryValueRepository
{
    protected $model, $model_key, $repository, $model_name;

    public function __construct(CategoryValue $category_value, CategoryRepository $category_repository)
    {
        $this->model = $category_value;
        $this->model_key = "catalog.category.values";
        $this->repository = $category_repository;
        $this->model_name = "Category";
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

            $config_arrays = collect(config('category.attributes'))->pluck('elements')->flatten(1)->toArray();

            foreach($config_arrays as $config_array){
                $key = $config_array["title"];

                if($this->repository->scopeFilter($data['scope'], $config_array["scope"])) {
                    $input[$key] = $this->repository->getDefaultValues($match)->$key;
                    continue;
                }
                $val = isset($data["attributes"][0][$key]) ? $data["attributes"][0][$key] : null;
                if($key == "image" && isset($val["value"]))
                {
                    $input[$key] = $this->storeImage($val["value"], strtolower($this->model_name));
                    continue;
                }
                if($val && isset($val["use_default_value"]) && $val["use_default_value"] != 1) throw ValidationException::withMessages([ "use_default_value" => __("core::app.response.use_default_value") ]);
                if($val) $input[$key] = (isset($val["use_default_value"]) && $val["use_default_value"] == 1) ? $this->repository->getDefaultValues($match)->toArray()[$key] : $val["value"];            
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

    public function storeImage(object $request, ?string $folder = null, ?string $delete_url = null): string
    {
        try
        {
            // Store File
            $file = $request;
            $key = Str::random(6);
            $folder = $folder ?? "default";
            $file_path = $file->storeAs("images/{$folder}/{$key}", (string) $file->getClientOriginalName());

            // Delete old file if requested
            if ( $delete_url !== null ) Storage::delete($delete_url);
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        return $file_path;
    }
}
