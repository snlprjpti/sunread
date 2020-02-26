<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponseFormat;


class BaseController extends Controller
{
    use ApiResponseFormat ,ValidatesRequests;
}
