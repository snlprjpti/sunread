<?php

namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Category\Contracts\CategoryInterface;
use Modules\Category\Repositories\CategoryTranslationRepository;

class CategoryRepository implements CategoryInterface
{
    protected $model, $model_key;

    public function __construct(Category $category, CategoryTranslationRepository $categoryTranslationRepository)
    {
        $this->model = $category;
        $this->translation = $categoryTranslationRepository;
        $this->model_key = "catalog.category";
    }

    /**
     * Get current Model
     * 
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Create a new resource
     * 
     * @param array $data
     * @param array $translation_data
     * @return Model
     */
    public function create($data, $translation_data = [])
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created = $this->model->create($data);
            $this->translation->createOrUpdate([$translation_data], $created);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
        DB::commit();

        return $created;
    }

    /**
     * Update requested resource
     * 
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update($data, $id, $translation_data = [])
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();

            $this->translation->createOrUpdate([$translation_data], $updated);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $updated);
        DB::commit();

        return $updated;
    }

    /**
     * Delete requested resource
     * 
     * @param int $id
     * @return Model
     */
    public function delete($id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $deleted = $this->model->findOrFail($id);
            if ( $deleted->image !== null ) Storage::disk('public')->delete($deleted->image);
            
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Delete requested resources in bulk
     * 
     * @param Request $request
     * @return Model
     */
    public function bulkDelete($request)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $request->validate([
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs,id',
            ]);

            $deleted = $this->model->whereIn('id', $request->ids);
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Returns validation rules
     * 
     * @param int $id
     * @param array $merge
     * @return array
     */
    public function rules($id, $merge = [])
    {
        $id = $id ? ",{$id}" : null;

        return array_merge([
            "slug" => "nullable|unique:categories,slug{$id}",
            "image" => "sometimes|mimes:jpeg,jpg,bmp,png",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean",
            "parent_id" => "sometimes|exists:categories,id"
        ], $merge);
    }

    /**
     * Validates form request
     * 
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function validateData($request, $id=null)
    {
        $data = $request->validate($this->rules($id));
        if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

        return $data;
    }

    /**
     * Translations validation
     * 
     * @param Request $request
     * @return array
     */
    public function validateTranslation($request)
    {
        return $request->validate([
            "name" => "required",
            "description" => "nullable",
            "meta_title" => "nullable",
            "meta_description" => "nullable",
            "meta_keywords" => "nullable",
            "locale" => "required|exists:locales,code"
        ]);
    }
}
