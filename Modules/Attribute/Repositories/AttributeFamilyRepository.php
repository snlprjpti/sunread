<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Attribute\Contracts\AttributeFamilyInterface;
use Modules\Attribute\Exceptions\AttributeGroupsPresent;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;

class AttributeFamilyRepository implements AttributeFamilyInterface
{
    protected $model, $model_key;

    public function __construct(AttributeFamily $attribute_family)
    {
        $this->model = $attribute_family;
        $this->model_key = "catalog.attribite";
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

            // Do not allow deleting
            if ($deleted->slug == 'default') throw new DefaultFamilyCanNotBeDeleted("Default family cannot be deleted.");
            if ( count($deleted->attributeGroups) > 0 ) throw new AttributeGroupsPresent("Attribute Groups present in family.");

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
            "slug" => "nullable|unique:attribute_families,slug{$id}",
            "name" => "required"
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
