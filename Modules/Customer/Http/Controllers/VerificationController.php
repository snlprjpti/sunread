<?php

namespace Modules\Customer\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Exception;

class VerificationController extends BaseController
{
    public function __construct( Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Customer";
        parent::__construct($this->model, $this->model_name);
    }

    public function verifyAccount($token)
    {
        try
        {
            $customer = $this->model::where('verification_token', $token)->firstOrFail();

            if(!is_null($customer)) {

                if(!$customer->is_email_verified) {
                    $customer->is_email_verified = 1;
                    $customer->verification_token = null;
                    $customer->save();
                    $message = $this->lang('verification-success');
                } else {
                    $message = $this->lang('already-verified');
                }
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($message, 201);
    }
}
