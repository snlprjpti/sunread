<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Exception;
use Modules\Notification\Events\ConfirmEmail;

class VerificationController extends BaseController
{
    public function __construct( Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Customer";
        parent::__construct($this->model, $this->model_name);
    }

    public function verifyAccount(string $token): JsonResponse
    {
        try
        {
            $customer = $this->model::where('verification_token', $token)->firstOrFail();

            if(!$customer->is_email_verified) {
                $customer->is_email_verified = 1;
                $customer->verification_token = null;
                $customer->save();
                $message = $this->lang('verification-success');

                event(new ConfirmEmail($customer->id));
            }
            else {
                $message = $this->lang('already-verified');
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($message, 201);
    }
}
