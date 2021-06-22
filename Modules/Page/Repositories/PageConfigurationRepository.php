<?php

namespace Modules\Page\Repositories;

use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\PageConfiguration;
use Modules\Page\Rules\PageConfigurationRule;

class PageConfigurationRepository extends BaseRepository
{
    private $pageConfiguration;

    public function __construct(PageConfiguration $pageConfiguration)
    {
        $this->model = $pageConfiguration;
        $this->model_key = "page.configuration";
        $model_types_in = implode(",", config('page.model_config'));
        $this->rules = [
            "title" => "required",
            "description" => "required",
            "scope" => "required|in:{$model_types_in}",
            "scope_id" => "required|numeric",
            "status" => "sometimes|boolean"
        ];
    }

    public function add(object $request): object
    {
        $item['scope'] = $request->scope;
        $item['scope_id'] = $request->scope_id;

        return (object) $created_data;
    }

    public function checkCondition(object $request): object
    {
        return $this->model->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['path', $request->path]
        ]);
    }

    public function getValues(object $request): mixed
    {
        return $this->checkCondition($request)->first()->value;
    }
}
