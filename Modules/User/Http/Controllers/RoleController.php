<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Role;

/**
 * Admin user role controller
 */
class RoleController extends BaseController
{
    public function __construct()
    {

    }

    public function index()
    {
        return $this->successResponse(200, $payload = Role::all());
    }

    public function show($id)
    {
        return $this->successResponse(200, $payload = Role::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'permission_type' => 'required',
            'description' => 'required',
            'permissions' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }
        $params = $request->all();
        $params = array_merge($params, ['slug' => Str::slug($params['name'])]);
        try {
            $role = Role::create($params);
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
        return $this->successResponse(201, $role, "Role created Successfully");
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'permission_type' => 'required',
            'description' => 'required',
            'permissions' => 'required|array'
        ]);
        $params = $request->all();
        $role = Role::find($id);
        try {
            $role = $role->update($params);
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
        return $this->successResponse(201, $role, "Role update Successfully");
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if ($role->slug == 'super-admin') {
            $this->errorResponse(403, "Super Admin cannot be deleted");
        }
        $role->delete();
        return $this->successResponse(400, null, "Role deleted success");
    }

}