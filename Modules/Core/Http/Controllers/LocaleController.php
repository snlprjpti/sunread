<?php

namespace Modules\Core\Http\Controllers;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Store;

class LocaleController implements Arrayable, ArrayAccess
{
    public function __construct(Store $store)
    {
        
    }




}
