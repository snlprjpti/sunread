<?php

namespace Modules\Page\Repositories;

use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;
use Modules\Page\Exceptions\PageTranslationDoesNotExist;
use Exception;

class PageRepository extends BaseRepository
{
    private $pageConfiguration, $store, $channel;

    public function __construct(Page $page, PageConfiguration $pageConfiguration, Store $store, Channel $channel)
    {
        $this->model = $page;
        $this->model_key = "page";
        $this->rules = [
            "parent_id" => "sometimes|numeric|exists:pages,id",
            "slug" => "nullable|unique:pages,slug",
            "title" => "required",
            "description" => "required",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "translations" => "nullable|array"
        ];
        $this->pageConfiguration = $pageConfiguration;
        $this->store = $store;
        $this->channel = $channel;
    }


    public function validateTranslationData(?array $translations): bool
    {
        if (empty($translations)) return false;

        foreach ($translations as $translation) {
            if (!array_key_exists("store_id", $translation) || !array_key_exists("title", $translation)) return false;
        }

        return true;
    }

    public function validateTranslation(object $request): void
    {
        $translations = $request->translations;
        if (!$this->validateTranslationData($translations)) {
            throw new PageTranslationDoesNotExist(__("core::app.response.missing-data", ["title" => "Page"]));
        }
    }

    public function checkCondition(object $request): object
    {
        return $this->pageConfiguration->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['page_id', $request->page_id]
        ]);
    }

    public function getPageDetail(object $page): object
    {
        try
        {
            $result = $this->checkCondition($page)->first();
            $configValue = (config('page.model_config'));

            if (!$result)
            {
                foreach($configValue as $key => $value)
                {
                    $scopeId = $data["scope_id"] ?? $page->scope_id;
                    $relation = $value["parent"];
                    if($relation != null && $page->scope != $value["parent_scope"])
                    {
                        $data["scope_id"] = (app($value["scope"])->find($scopeId)->$relation->id);
                        $data["page_id"] = $page->page_id;
                        $data["scope"] = $value["parent_scope"];
                        $result = $this->checkCondition((object) $data)->first();
                        if(isset($result)) break;
                    }
                }
            }

            if(!$result)
            {
                $result = $this->page($page);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $result;
    }

    public function page(object $data): object
    {
        try
        {
            $result = $this->model->findOrFail($data->page_id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $result;
    }
}
