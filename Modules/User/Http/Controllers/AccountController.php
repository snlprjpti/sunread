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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        try {
            $me = auth()->guard('admin')->user();
            return $this->successResponse(200, $me);
        } catch (UnauthorizedHttpException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
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
            $data = request()->input();
            if (!Hash::check($data['current_password'], auth()->guard('admin')->user()->password)) {
                return $this->errorResponse(401, "Invalid password. Fail to update");
            }
            if (isset($data['password']))
                $data['password'] = bcrypt($data['password']);
            $user->update($data);
            return $this->successResponse(200, null, "Account updated successfully");
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }
}
