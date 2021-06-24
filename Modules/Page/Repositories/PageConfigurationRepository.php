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
        $this->rules = [
            "scope" => [ "sometimes", "in:website,channel" ]
        ];
    }

    public function scopeValidation(object $request)
    {
        return ((isset($request->scope) && $request->scope != "website") || isset($request->scope_id)) ? [
            "scope_id" => ["required", "integer", "min:0", new PageConfigurationRule($request->scope)]
        ] : [];
    }

    public function add(object $request): object
    {
        $item['scope'] = $request->scope;
        $item['scope_id'] = $request->scope_id;

        foreach($request->items as $key => $val) {
            $item['path'] = $key;
            $item['value'] = $val;
            if ($configData = $this->checkCondition((object)$item)->first()) {
                $created_data['data'][] = $this->update($item, $configData->id);
            }else{
                $created_data['data'][] = $this->create($item);
            }
        }
        $created_data['message'] = 'create-success';
        $created_data['code'] = 201;
        return (object) $created_data;
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "website" && in_array($element_scope, ["website"])) return true;
        if($scope == "channel" && in_array($element_scope, ["global", "website"])) return true;
        return false;
    }

    public function checkCondition(object $request): object
    {
        return $this->model->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['path', $request->path]
        ]);
    }
}
