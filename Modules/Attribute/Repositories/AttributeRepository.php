<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Contracts\AttributeInterface;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Repositories\AttributeTranslationRepository;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;

class AttributeRepository implements AttributeInterface
{
    protected $translation, $option, $model, $model_key, $attribute_types, $non_filterable_fields;

    public function __construct(Attribute $attribute, AttributeTranslationRepository $attributeTranslationRepository, AttributeOptionRepository $attributeOptionRepository)
    {
        $this->model = $attribute;
        $this->translation = $attributeTranslationRepository;
        $this->option = $attributeOptionRepository;
        $this->model_key = "catalog.attribite";
        $this->attribute_types = [
            "text" => "Text",
            "textarea" => "Textarea",
            "price" => "Price",
            "boolean" => "Boolean",
            "select" => "Select",
            "multiselect" => "Multiselect",
            "datetime" => "Datetime",
            "date" => "Date",
            "image" => "Image",
            "file" => "File",
            "checkbox" => "Checkbox"
        ];
        $this->non_filterable_fields = ["select", "multiselect", "checkbox"];
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
     * @return Model
     */
    public function create($data)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created = $this->model->create($data);
            $this->translation->createOrUpdate($data['translations'], $created);
            if (in_array($data['type'], $this->non_filterable_fields)) $this->option->createOrUpdate($data['attribute_options'], $created);
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
    public function update($data, $id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();

            $this->translation->createOrUpdate($data['translations'], $updated);
            if (in_array($data['type'], $this->non_filterable_fields)) $this->option->createOrUpdate($data['attribute_options'], $updated);
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
        $attribute_types = implode(",", array_keys($this->attribute_types));

        return array_merge([
            "slug" => "nullable|unique:attributes,slug{$id}",
            "name" => "required",
            "type" => "required|in:{$attribute_types}",
            "position" => "sometimes|numeric",
            "is_required" => "sometimes|boolean",
            "is_unique" => "sometimes|boolean",
            "validation" => "nullable",
            "is_visible_on_front" => "sometimes|boolean",
            "is_user_defined" => "sometimes|boolean",
            "use_in_flat" => "sometimes|boolean",
            "attribute_group_id" =>  "nullable|exists:attribute_groups,id",
            "translations" => "nullable"
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
        if (!in_array($request->type, $this->non_filterable_fields)) $data["is_filterable"] = 0;
        // TODO::future, available for development only
        $data["use_in_flat"] = 0;

        return $data;
    }

    /** Checks if locale is present in translation
     * 
     * @param array $translations
     * @return boolean
     */
    public function validateTranslationData($translations)
    {
        if (empty($translations)) return false;

        foreach ($translations as $translation) {
            if (!array_key_exists('locale', $translation) || !array_key_exists('name', $translation)) return false;
        }

        return true;
    }

    /**
     * Translations validation
     * 
     * @param Request $request
     * @throws AttributeTranslationDoesNotExist
     * @throws AttributeTranslationOptionDoesNotExist
     */
    public function validateTranslation($request)
    {
        $translations = $request->translations;
        if (!$this->validateTranslationData($translations)) {
            throw new AttributeTranslationDoesNotExist("Missing attribute translation.");
        }

        $options = $request->attribute_options;
        if (is_array($options) && in_array($request->type, $this->non_filterable_fields)) {
            foreach ($options as $option) {
                if (!isset($option["translations"]) || !$this->validateTranslationData($option["translations"])) {
                    throw new AttributeTranslationOptionDoesNotExist("Missing Attribute Option translation.");
                }
            }
        }
    }
}
