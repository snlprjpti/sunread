<?php

namespace Modules\Customer\Http\Controllers;

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
     * @return Response
     */
    public function register()
    {
        try{
            $this->validate(request(), [
                'first_name' => 'string|required',
                'last_name' => 'string|required',
                'email' => 'email|required|unique:customers,email',
                'password' => 'confirmed|min:6|required',
            ]);
            $data = request()->input();
            $data['password'] = bcrypt($data['password']);
            $verificationData['email'] = $data['email'];

            $verificationData['token'] = md5(uniqid(rand(), true));
            //TODO::future => Send email verification for registered user

            $data['token'] = $verificationData['token'];
            $customer = Customer::create($data);
            return $this->successResponse(200,$customer, trans('core::app.response.create-success', ['name' => 'Account']));
        }catch (ValidationException $exception){
            return $this->errorResponse(400, $exception->errors());
        }catch (\Exception $exception){
            return $this->errorResponse(400,$exception->getMessage());
        }

    }



}
