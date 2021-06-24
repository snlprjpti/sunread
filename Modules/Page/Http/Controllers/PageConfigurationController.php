<?php

namespace Modules\Page\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\PageConfiguration;

class PageConfigurationController extends BaseController
{

    public function __construct(PageConfiguration $pageConfiguration)
    {
        $this->model = $pageConfiguration;
        $this->model_name = "Page Configuration";

        parent::__construct($this->model, $this->model_name);
    }
}
