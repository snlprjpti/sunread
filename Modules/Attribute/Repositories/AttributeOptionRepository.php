<?php


namespace Modules\Attribute\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function createOrUpdateAttributeOption(array $data)
    {
        try {
            if (isset($data['attribute_option_id'])) {
                $attributeOption = $this->model->findOrFail($data['attribute_option_id']);
            }else{
                $attributeOption =  new AttributeOption();
            }
            $attributeOption->fill($data);
            $attributeOption->save();
            $this->createUpdateOptionTranslation($data,$attributeOption);
            return $attributeOption;

        }catch (ModelNotFoundException $exception){
            throw $exception;
        }

    }


    public function createUpdateOptionTranslation(Array $optionInputs,$attributeOption)
    {
        try {
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
        }catch (\Exception $exception){
            throw $exception;
        }

    }



}
