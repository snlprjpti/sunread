<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;

/**
 * Forgot Password controller for the customer.
 *
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class ForgotPasswordController extends BaseController
{

    use SendsPasswordResetEmails;


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['email' => 'required|email']);
            if($validator->fails()){
               return  $this->errorResponse(400,$validator->errors());
            }

            $email  = $request->get('email');
            $admin = Admin::where('email', $email)->first();
            if(!$admin){
                return  $this->errorResponse(400,"Email doesnt exist");
            }
            $response = $this->broker()->sendResetLink(request(['email']));

            if ($response == Password::RESET_LINK_SENT) {
                return $this->successResponse(200,$admin,"Reset Link sent to your email {$admin->email}");
            }
        } catch (\Exception $e) {
           dd($e);
            return $this->errorResponse(400,$e->getMessage());
        }
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }
}