<?php


namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\AttributeOption;

class AttributeOptionRepository
{
    protected $model, $model_key, $translation;

    public function __construct(AttributeOption $attribute_option, AttributeOptionTranslationRepository $attributeOptionTranslationRepository)
    {
        $this->model = $attribute_option;
        $this->translation = $attributeOptionTranslationRepository;
        $this->model_key = "catalog.attribute.options";
    }

    public function updateOrCreate(array $data, object $parent): void
    {
        if ( !is_array($data) || count($data) ) return;

        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            foreach ($data as $row){
                $check = [
                    "attribute_option_id" => $row["attribute_option_id"],
                    "attribute_id" => $parent->id
                ];
    
                $created = $this->model->firstorNew($check);
                $created->fill($row);
                $created->save();

                $this->translation->updateOrCreate($data, $created);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
    }
}
