<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;

class UserController extends BaseController
{
    public function index()
    {
        return $this->successResponse(200, $payload = Admin::all());
    }

    public function show($id)
    {
        return $this->successResponse(200, $payload = Admin::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:admins,email',
            'password' => 'nullable|confirmed',
            'status' => 'required|boolean',
            'role_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }
        $params = $request->all();
        if (isset($params['password']) && $params['password']) {
            $params['password'] = bcrypt($params['password']);
        }
        try {
            $admin = Admin::create($params);
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
        return $this->successResponse(201, $admin, "Admin created Successfully");
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . $id,
            'password' => 'nullable|confirmed',
            'status' => 'required|boolean',
            'role_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }

        $params = $request->all();
        if (isset($params['password']) && $params['password']) {
            $params['password'] = bcrypt($params['password']);
        }
        try {
            $admin = Admin::find($id);
            $admin = $admin->update($params);
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
        return $this->successResponse(201, $admin, "Admin updated Successfully");
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);
        $me = auth()->guard('admin')->user();
        if ($admin->slug == 'super-admin') {
            $this->errorResponse(400, "Cannot destroy super admin");
        }
        if ($me->role->slug === 'super-admin') {
            $admin->delete();
            return $this->successResponse(400, null, "Admin deleted success");
        }
    }

}
