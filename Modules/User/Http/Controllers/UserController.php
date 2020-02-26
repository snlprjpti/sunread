<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;

/**
 * User Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class UserController extends BaseController
{

    /**
     * returns all the admins
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            return $this->successResponse(200, Admin::all());
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }

    /**
     * Get the particular admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->successResponse(200, $payload = Admin::findOrFail($id));
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * store the new admin resource
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $this->validate($request, Admin::rules());
            if (isset($params['password']) && $params['password']) {
                $params['password'] = bcrypt($params['password']);
            }
            $admin = Admin::create($params);
            return $this->successResponse(201, $admin, "Admin created Successfully");
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Update the admin details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $params = $request->all();
            $this->validate($params, Admin::rules($id));
            if (isset($params['password']) && $params['password']) {
                $params['password'] = bcrypt($params['password']);
            }
            $admin = Admin::find($id);
            $admin = $admin->update($params);
            return $this->successResponse(200, $admin, "Admin updated Successfully");
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }


    /**
     * Remove the specified  admin resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $admin = Admin::find($id);
            $admin->delete();
            return $this->successResponse(400, null, "Admin deleted success");
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

}
