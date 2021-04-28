<?php

namespace Modules\Category\Repositories;

use Modules\Category\Entities\Category;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $model, $model_key, $repository;

    public function __construct(Category $category)
    {
        $this->model = $category;
        $this->model_key = "category.categories";
        $this->rules = [
            // category validation
            "name" => "required",
            "slug" => "nullable|unique:categories,slug",
            "position" => "sometimes|numeric",
            "image" => "sometimes|mimes:jpeg,jpg,bmp,png",
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

    // public function model(): Model
    // {
    //     return $this->model;
    // }

    // public function create(array $data, array $translation_data = []): object
    // {
    //     DB::beginTransaction();
    //     Event::dispatch("{$this->model_key}.create.before");

    //     try
    //     {
    //         $created = $this->model->create($data);
    //         $this->translation->createOrUpdate($translation_data, $created);
    //     }
    //     catch (Exception $exception)
    //     {
    //         DB::rollBack();
    //         throw $exception;
    //     }

    //     Event::dispatch("{$this->model_key}.create.after", $created);
    //     DB::commit();

    //     return $created;
    // }

    // public function update(array $data, int $id, array $translation_data = []): object
    // {
    //     DB::beginTransaction();
    //     Event::dispatch("{$this->model_key}.update.before");

    //     try
    //     {
    //         $updated = $this->model->findOrFail($id);
    //         $updated->fill($data);
    //         $updated->save();

    //         $this->translation->createOrUpdate($translation_data, $updated);
    //     }
    //     catch (Exception $exception)
    //     {
    //         throw $exception;
    //     }

    //     Event::dispatch("{$this->model_key}.update.after", $updated);
    //     DB::commit();

    //     return $updated;
    // }

    // public function delete(int $id): object
    // {
    //     DB::beginTransaction();
    //     Event::dispatch("{$this->model_key}.delete.before");

    //     try
    //     {
    //         $deleted = $this->model->findOrFail($id);
    //         if ( $deleted->image !== null ) Storage::disk('public')->delete($deleted->image);
            
    //         $deleted->delete();
    //     }
    //     catch (Exception $exception)
    //     {
    //         throw $exception;
    //     }

    //     Event::dispatch("{$this->model_key}.delete.after", $deleted);
    //     DB::commit();

    //     return $deleted;
    // }

    // public function bulkDelete(object $request): object
    // {
    //     DB::beginTransaction();
    //     Event::dispatch("{$this->model_key}.delete.before");

    //     try
    //     {
    //         $request->validate([
    //             'ids' => 'array|required',
    //             'ids.*' => 'required|exists:activity_logs,id',
    //         ]);

    //         $deleted = $this->model->whereIn('id', $request->ids);
    //         $deleted->delete();
    //     }
    //     catch (Exception $exception)
    //     {
    //         throw $exception;
    //     }

    //     Event::dispatch("{$this->model_key}.delete.after", $deleted);
    //     DB::commit();

    //     return $deleted;
    // }

    // public function rules(?int $id, array $merge = []): array
    // {
    //     $id = $id ? ",{$id}" : null;

    //     return array_merge([
    //         "name" => "required",
    //         "slug" => "nullable|unique:categories,slug{$id}",
    //         "position" => "sometimes|numeric",
    //         "image" => "sometimes|mimes:jpeg,jpg,bmp,png",
    //         "description" => "sometimes|nullable",
    //         "meta_title" => "sometimes|nullable",
    //         "meta_description" => "sometimes|nullable",
    //         "meta_keywords" => "sometimes|nullable",
    //         "status" => "sometimes|boolean",
    //         "parent_id" => "sometimes|exists:categories,id"
    //     ], $merge);
    // }

    // public function validateData(object $request, ?int $id=null, array $merge = []): array
    // {
    //     $data = $request->validate($this->rules($id, $merge));
    //     if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

    //     return $data;
    // }

    // public function validateTranslation(object $request): array
    // {
    //     if ( !is_array($request->translation) || $request->translation == [] ) return [];

    //     return $request->validate([
    //         "translation.name" => "sometimes|required",
    //         "translation.description" => "sometimes|nullable",
    //         "translation.meta_title" => "sometimes|nullable",
    //         "translation.meta_description" => "sometimes|nullable",
    //         "translation.meta_keywords" => "sometimes|nullable",
    //         "translation.store_id" => "required|exists:stores,id"
    //     ]);
    // }
}
