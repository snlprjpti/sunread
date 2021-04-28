<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Currency;

class CurrencyRepository extends BaseRepository
{
    public function __construct(Currency $currency)
    {
        $this->model = $currency;
        $this->model_key = "core.currency";
        $this->rules = [
            /* General */
            "code" => "required|min:3|max:3|unique:currencies,code",
            "name" => "required",
            "symbol" => "nullable"
        ];
    }
}
