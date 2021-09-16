<?php

namespace Modules\EmailTemplate\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Modules\EmailTemplate\Repositories\EmailTemplateRepository;
use Exception;

class EmailTemplateGroupController extends BaseController
{
    public function __construct(EmailTemplate $emailTemplate, EmailTemplateRepository $emailTemplateRepository)
    {
        $this->model = $emailTemplate;
        $this->model_name = "Email Template";
        $this->repository = $emailTemplateRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function index(Request $request)
    {
        try
        {
            $fetched = $this->repository->getConfigData($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang('fetch-list-success'));
    }
}
