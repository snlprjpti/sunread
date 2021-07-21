<?php

namespace Modules\Page\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Page\Entities\PageScope;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Core\Rules\ScopeRule;

class PageScopeRepository extends BaseRepository
{
    public function __construct(PageScope $pageScope)
    {
        $this->model = $pageScope;
        $this->model_key = "page.scope";
        $this->rules = [
            "scope" => "required|in:website,store",
            "scope_id" => "required|numeric"
        ];
    }

    public function updateOrCreate(array $scopes, object $parent):void
    {
        if ( !is_array($scopes) || count($scopes) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");
        try
        {
            $page_scopes = [];
            foreach($scopes as $scope)
            {  
                $data = $this->validateData(new Request($scope), [
                    "scope_id" => [ "required", "numeric", new ScopeRule($scope["scope"]) ]
                ]);
                $data["page_id"] = $parent->id;

                if($exist = $this->model->where($data)->first())
                {
                    $page_scopes[] = $exist;
                    continue;
                }
                $page_scopes[] = $this->create($data);
            }
            $parent->page_scopes()->whereNotIn('id', array_filter(Arr::pluck($page_scopes, 'id')))->delete();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $page_scopes);
        DB::commit();
    }
}
