<?php


namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Http\Request;

class AttributeOptionRepository extends BaseRepository
{
    protected $model, $model_key, $translation;

    public function __construct(AttributeOption $attribute_option, AttributeOptionTranslationRepository $attributeOptionTranslationRepository)
    {
        $this->model = $attribute_option;
        $this->translation = $attributeOptionTranslationRepository;
        $this->model_key = "catalog.attribute.options";
        $this->rules = [
            "name" => "required",
            "position" => "sometimes|numeric",
            "translations" => "nullable|array"
        ];
    }

    public function updateOrCreate(?array $data, object $parent, $method=null): void
    {
        if ( count($data) == 0 ) return;

        Event::dispatch("{$this->model_key}.create.before");
        $items = [];
        try
        {
            if($method == "update") $parent->attribute_options()->whereNotIn('id', array_filter(Arr::pluck($data, 'id')))->delete();

            foreach ($data as $row){
                $this->validateData(new Request($row), isset($row["id"]) ? [
                    "id" => "exists:attribute_options,id"
                ] : []);
                
                $row['attribute_id'] = $parent->id;
                $created = !isset($row["id"]) ? $this->create($row) : $this->update($row, $row["id"]);

                $this->translation->updateOrCreate($row["translations"], $created);
                $items[] = $created;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $items);
    }
}
