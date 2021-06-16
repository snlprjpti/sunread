<?php

namespace Modules\Page\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Modules\Page\Exceptions\PageTranslationDoesNotExist;

class PageRepository extends BaseRepository
{
    public function __construct(Page $page)
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
            throw new PageTranslationDoesNotExist(__("core.app.response.missing-data", ["title" => "Page"]));
        }
    }
}
