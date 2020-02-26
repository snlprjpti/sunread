<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;
use Modules\User\Entities\Role;

/**
 * Role controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RoleController extends BaseController
{
    /**
     * RoleController constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Returns all the roles
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse(200, $payload = Role::all());
    }

    /**
     * Get the particular role
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->successResponse(200, $payload = Role::findOrFail($id));
    }

    /**
     * Stores new role
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make($params, Role::rules());
        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }

        try {
            $params = array_merge($params, ['slug' => Str::slug($params['name'])]);
            $role = Role::create($params);
            return $this->successResponse(201, $role, "Role created Successfully");
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
    }


    /**
     * Updates the role details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $params = $request->all();
        $validator = Validator::make(request()->all(), Admin::rules($id));
        if ($validator->fails()) {
            return $this->errorResponse(400, $validator->errors());
        }
        try {
            $role = Role::find($id);
            $role = $role->update($params);
            return $this->successResponse(201, $role, "Role update Successfully");
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Destroys the particular role
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();
            return $this->successResponse(400, null, "Role deleted success");
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }

    }

}