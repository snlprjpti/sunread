<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Exception;
use Modules\Notification\Events\ConfirmEmail;
use Modules\Notification\Events\NewAccount;

class VerificationController extends BaseController
{
    public function __construct( Customer $customer)
    {
        $this->model = $customer;
        $this->model_name = "Customer";
        parent::__construct($this->model, $this->model_name);
    }

    public function sendConfirmation(): JsonResponse
    {
        try
        {
            $customer = auth()->guard('customer')->user();

            if(!$customer->is_email_verified) {
                if(SiteConfig::fetch("require_email_confirmation", "website", $customer->website_id) == 1) {
                    $customer["verification_token"] = Str::random(30);
                    $customer->save();
                    $message = "response.send-confirmation-link";

                    event(new NewAccount($customer->id, $customer->verification_token));
                }
            }
            else {
                $message = "response.already-verified";
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang($message), 201);
    }

    public function verifyAccount(string $token): JsonResponse
    {
        try
        {
            $customer = $this->model::where("verification_token", $token)->firstOrFail();

            if(!$customer->is_email_verified) {
                $customer->is_email_verified = 1;
                $customer->verification_token = null;
                $customer->save();
                $message = "response.verification-success";

                event(new ConfirmEmail($customer->id));
            }
            else {
                $message = "response.already-verified";
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang($message), 201);
    }
}
