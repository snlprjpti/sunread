<?php


namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Modules\Category\Entities\CategoryTranslation;

class CategoryTranslationRepository
{
    protected $model, $model_key;

    public function __construct(CategoryTranslation $attribute_translation)
    {
        $this->model = $attribute_translation;
        $this->model_key = "catalog.attribite.translations";
    }

    /**
     * Create or update translation
     * 
     * @param array $data
     * @param Model $parent
     * @return void
     */
    public function createOrUpdate($data, $parent)
    {
        if ( !is_array($data) ) return;

        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            foreach ($data as $row){
                $check = [
                    "locale" => $row["locale"],
                    "category_id" => $parent->id
                ];
    
                $created = $this->model->firstorNew($check);
                $created->fill($row);
                $created->save();
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
    }
}
