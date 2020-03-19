<?php


namespace Modules\Attribute\Repositories;

use Modules\Attribute\Contracts\AttributeOptionInterface;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Attribute\Entities\AttributeOptionTranslation;
use Modules\Core\Eloquent\Repository;

class AttributeOptionRepository  extends Repository implements AttributeOptionInterface
{


    /**
     * @inheritDoc
     */
    public function model()
    {
        return AttributeOption::class;
    }

    public function createAttributeOption(array $data)
    {
        $attributeOption = $this->model->create($data);
        $attributeOption = $this->createUpdateOptionTranslation($data,$attributeOption);
        return $attributeOption;
    }


    public function createUpdateOptionTranslation(Array $optionInputs,$attributeOption)
    {
        if(isset($optionInputs['translations'])){
            $translated_options = $optionInputs['translations'];
            foreach ($translated_options as $translated_option){
                $check_attributes = ['locale' => $translated_option['locale'], 'attribute_option_id' => $attributeOption->id];
                $optionInputs = array_merge($translated_option, $check_attributes);
                $option_translation = AttributeOptionTranslation::firstOrNew($check_attributes);
                $option_translation->fill($optionInputs);
                $option_translation->save();

            }

        }
    }



}
