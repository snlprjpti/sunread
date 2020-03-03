<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;


/**
 * Registration Controller customer
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RegistrationController extends BaseController
{

    /**
     * Method to store user's sign up form data to DB.
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        try {
            $this->validate(request(), [
                'first_name' => 'string|required',
                'last_name' => 'string|required',
                'email' => 'email|required|unique:customers,email',
                'password' => 'confirmed|min:6|required',
            ]);

            $request->merge(['password' => bcrypt($request->get('password'))]);

            $email = $request->get('email');
            $token = md5(uniqid(rand(), true));
            //TODO::future => Send email verification for registered user

            $customer = Customer::create($request->only(['first_name', 'last-name', 'email', 'password']));
            return $this->successResponseWithMessage($customer, trans('core::app.response.create-success', ['name' => 'Account']), 200);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


}
