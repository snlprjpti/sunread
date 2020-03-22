<?php

namespace Modules\Attribute\Repositories;

use Illuminate\Support\Facades\Event;
use Modules\Attribute\Contracts\AttributeInterface;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;

class AttributeRepository extends Repository implements AttributeInterface
{

    protected $attributeOptionRepository;
    public function __construct(AttributeOptionRepository $attributeOptionRepository, App $app)
    {
        parent::__construct($app);
        $this->attributeOptionRepository = $attributeOptionRepository;
    }

    /**
     * @inheritDoc
     */
    public function model()
    {
        return Attribute::class;
    }

    public function createAttribute($request)
    {
        Event::dispatch('catalog.attribute.create.before');

        //storing attribute
        $attribute = $this->model->create(
            $request->only(
                ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'use_in_flat', 'attribute_group_id']
            )
        );

        //storing attributes-translation
        if (is_array($request->get('translations'))) {
            $this->createUpdateTranslation($request->get('translations'),$attribute);
        }

        //store attribute-option
        $options = $request->get('attribute_options');
        if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
            foreach ($options as $optionInputs) {
                $this->attributeOptionRepository->createOrUpdateAttributeOption(
                    array_merge(
                        $optionInputs,
                        ['attribute_id' => $attribute->id]
                    )
                );
            }
        }

        Event::dispatch('catalog.attribute.create.after',$attribute);

        return $attribute;
    }

    public function createUpdateTranslation(Array $translation_attributes,$attribute)
    {
        foreach ($translation_attributes as $translation_attribute){
            $check_attributes = ['locale' => $translation_attribute['locale'], 'attribute_id' => $attribute->id];
            $attribute_translation = AttributeTranslation::firstorNew($check_attributes);
            $attribute_translation->fill($translation_attribute);
            $attribute_translation->save();
        }

    }

    //updates attributes and translation
    public function updateAttributes($request ,$id)
    {
        $attribute = $this->model->findOrFail($id);
        $attribute->update(
            $request->only(
                ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'swatch_type', 'use_in_flat']
            )
        );

        //update attribute translation
        $this->createUpdateTranslation($request->get('translations'),$attribute);

        //store attribute-option and translation
        $options = $request->get('attribute_options');

        if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
            foreach ($options as $optionInputs) {
                 $this->attributeOptionRepository->createOrUpdateAttributeOption(
                     array_merge(
                         $optionInputs,
                         ['attribute_id' => $attribute->id]
                     )
                 );
            }
        }

    }

    public function rules($id = 0, $merge = [])
    {
        return array_merge([
            'slug' => ['nullable', 'unique:attributes,slug' . ($id ? ",$id" : '')],
            'name' => 'required',
            'type' => 'required',
            'attribute_group_id' => 'nullable|exists:attribute_groups,id'
        ], $merge);
    }

    public function delete($id)
    {
        Event::dispatch('catalog.attribute.delete.before', $id);

        parent::delete($id);

        Event::dispatch('catalog.attribute.delete.after', $id);

    }
}
