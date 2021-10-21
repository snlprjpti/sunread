<?php

namespace Modules\Customer\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Exceptions\CustomerNotFoundException;
use Modules\Customer\Exceptions\TokenGenerationException;

class VerificationController extends BaseController
{
    public function __construct( Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Customer";
        $exception_statuses = [
            TokenGenerationException::class => 401,
            CustomerNotFoundException::class => 404
        ];
        parent::__construct($this->model, $this->model_name,$exception_statuses);
    }

    public function verifyAccount($token)
    {

    }
}
