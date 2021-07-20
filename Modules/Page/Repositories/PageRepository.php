<?php

namespace Modules\Page\Repositories;

use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;
use Modules\Page\Exceptions\PageTranslationDoesNotExist;
use Exception;
use Illuminate\Validation\ValidationException;
use Modules\Core\Rules\ScopeRule;
use Modules\Product\Rules\WebsiteWiseScopeRule;

class PageRepository extends BaseRepository
{
    private $pageConfiguration, $store, $channel;

    public function __construct(Page $page, PageConfiguration $pageConfiguration, Store $store, Channel $channel)
    {
        $this->model = $page;
        $this->model_key = "page";
        $this->rules = [
            "title" => "required",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "translations" => "nullable|array",
            "page_scopes" => "required|array"
        ];
        $this->pageConfiguration = $pageConfiguration;
        $this->store = $store;
        $this->channel = $channel;
    }


    // public function validateScopeData(?array $translations): bool
    // {
    //     if (empty($scopes)) return false;

    //     foreach ($scopes as $scope) {
    //         if (!array_key_exists("scope", $scope) || !array_key_exists("scope_id", $scope)) return false;
    //         if(new ScopeRule());
    //     }

    //     return true;
    // }

    // public function validateScope(array $data): void
    // {
    //     $scopes = $data["page_scopes"];
    //     if (!$this->validateTranslationData($scopes)) {
    //         throw new PageTranslationDoesNotExist(__("core::app.response.missing-data", ["title" => "Page"]));
    //     }
    // }

    public function validateSlug(array $data): void
    {
        array_map(function($scope) use ($data) {
            $exist_slug = $this->model->whereSlug($data["slug"])->whereHas("page_scopes", function ($query) use ($scope) {
                $query->whereScope($scope["scope"])->whereScopeId($scope["scope_id"]);
            })->first();
            if($exist_slug) throw ValidationException::withMessages(["slug" => "Slug has already taken."]);
        }, $data["page_scopes"]);
    }
}
