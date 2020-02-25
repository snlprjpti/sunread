<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponseFormat;


class BaseController extends Controller
{
    use ApiResponseFormat;
}
