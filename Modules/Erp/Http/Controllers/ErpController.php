<?php

namespace Modules\Erp\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Erp\Repositories\ErpRepositiory;

class ErpController extends BaseController
{
    protected $repository;

    public function __construct(ErpRepositiory $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try
        {
            $this->repository->list($request);
        }
        catch ( Exception $exception )
        {
            return $this->handleException($exception);
        }
        return;
    }
}
