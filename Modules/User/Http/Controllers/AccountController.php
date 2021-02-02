<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Account Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class AccountController extends BaseController
{
    /**
     * Show the form for creating a new resource
     * @return JsonResponse
     */
    public function edit()
    {
        try {
            $me = auth()->guard('admin')->user();
            return $this->successResponse($me,"Admin User  fetched successfully.");

        } catch (UnauthorizedHttpException $exception) {
            return $this->errorResponse($exception->getMessage(), 401);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $user = auth()->guard('admin')->user();

            $this->validate($request, [
                'first_name' => 'sometimes|min:2|max:200',
                'last_name' => 'sometimes|min:2|max:200',
                'email' => 'email|unique:admins,email,' . $user->id,
                'password' => 'sometimes|required|min:6|confirmed|max:200',
                'current_password' => 'sometimes|min:6|max:200',
                'company' =>'sometimes|min:3|max:200',
                'address' =>'sometimes|min:3|max:200',
            ]);

            if ($request->has('password')) {
                $current_password = request()->get('current_password');
                if(is_null($current_password) || empty($current_password)){
                    throw ValidationException::withMessages([
                        "current_password" => "current password field is required"
                    ]);
                }
                if (!Hash::check($current_password, auth()->guard('admin')->user()->password)) {
                    return $this->errorResponse(trans('core::app.users.users.incorrect-password'), 401);
                }
                $request->merge(['password' => bcrypt($request->get('password'))]);
            }
            $user->fill($request->only('first_name','last_name','address','company', 'email', 'password'));
            $user->save();
            return $this->successResponse($user,trans('core::app.response.update-success', ['name' => 'Admin account']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }
}
