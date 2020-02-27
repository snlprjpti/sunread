<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        try {
            return $this->successResponse(200, $payload = Role::all());
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Get the particular role
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->successResponse(200, $payload = Role::findOrFail($id));
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Stores new role
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $this->validate($request, Role::rules());
            $params = array_merge($params, ['slug' => Str::slug($params['name'])]);
            $role = Role::create($params);
            return $this->successResponse(201, $role,  trans('core::app.response.create-success', ['name' => 'Role']));
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }


    /**
     * Updates the role details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();
            $this->validate($request, Admin::rules($id));
            $role = Role::find($id);
            $role = $role->update($params);
            return $this->successResponse(201, $role,  trans('core::app.response.update-success', ['name' => 'Role']));
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
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
            return $this->successResponse(400, null,  trans('core::app.response.d-success', ['name' => 'Role']));
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }

}