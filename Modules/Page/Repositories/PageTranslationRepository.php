<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Exception;
use Modules\Page\Entities\PageTranslation;

class PageTranslationRepository
{
    protected $model, $model_key;

    public function __construct(PageTranslation $pageTranslation)
    {
        $this->model_key = "page.translations";
        $this->model = $pageTranslation;
    }

    public function updateOrCreate(?array $data, object $parent): void
    {
        if ( !is_array($data) ) return;
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            foreach ($data as $row) {
                $check = [
                    "store_id" => $row["store_id"],
                    "page_id" => $parent->id
                ];

                $created = $this->model->firstorNew($check);
                $created->fill($row);
                $created->save();
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
        DB::commit();
    }
}
