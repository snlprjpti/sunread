<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;

class StoreRepository extends BaseRepository
{

    public function __construct(Store $store)
    {
        $this->model = $store;
        $this->model_key = "core.stores";
        $this->rules = [
            "currency" => "required|exists:currencies,code",
            "name" => "required",
            "slug" => "nullable|unique:stores,slug",
            "locale" => "required",
            "image" => "required|mimes:bmp,jpeg,jpg,png,webp",
            "position" => "sometimes|numeric"
        ];
    }

}
