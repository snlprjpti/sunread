<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\Event;
use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\FeatureTranslation;

class FeatureTranslationRepository extends BaseRepository
{
    public function __construct(FeatureTranslation $featureTranslation)
    {
        $this->model = $featureTranslation;
        $this->model_key = "feature.translations";
    }

    public function updateOrCreate(?array $data, object $parent): void
    {
        if ( !is_array($data) ) return;
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            foreach ($data as $row) {
                $check = [
                    "store_id" => $row["store_id"],
                    "feature_id" => $parent->id
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
