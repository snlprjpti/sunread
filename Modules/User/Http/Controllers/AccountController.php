<?php

namespace Modules\User\Http\Controllers;

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
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        try {
            $me = auth()->guard('admin')->user();
            return $this->successResponse($me);

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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {

            $user = auth()->guard('admin')->user();

            $this->validate($request, [
                'name' => 'required',
                'email' => 'email|unique:admins,email,' . $user->id,
                'password' => 'nullable|min:6|confirmed',
                'current_password' => 'required|min:6'
            ]);

            $current_password = request()->get('current_password');
            if (!Hash::check($current_password, auth()->guard('admin')->user()->password)) {
                return $this->errorResponse(trans('core::app.users.incorrect-password'), 401);
            }

            if ($request->get('password')) {
                $request->merge(['password' => bcrypt($request->get('password'))]);
            }

            $user->update($request->only('name', 'email', 'password'));

            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => 'Customer Account']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }
}
