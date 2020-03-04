<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponseFormat;


class BaseController extends Controller
{
    use ApiResponseFormat ,ValidatesRequests;

    protected $pagination_limit,$locale;
    public function __construct()
    {
        $this->pagination_limit = 25;

        //TODO ::future handle this variable in static memory in core helper
        $this->locale = config('locales.lang')? config('locales.lang'):config('app.locale');
    }

}
