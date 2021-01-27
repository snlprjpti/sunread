<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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


    protected $pagination_limit;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin');
    }

    /**
     * returns all the admins
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $payload = Admin::paginate($this->pagination_limit);
            return $this->successResponse($payload, "Admin fetched successfully.");

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Get the particular admin
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $payload = Admin::findOrFail($id);
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * store the new admin resource
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, Admin::rules());

            $password = $request->get('password');
            if (isset($password)) {
                $request->merge(['password' => bcrypt($password)]);
            }

            $admin = Admin::create(
                $request->only('name', 'email', 'password', 'role_id', 'status')
            );

            return $this->successResponse($admin, trans('core::app.response.create-success', ['name' => 'Admin']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->errors(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Update the admin details
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $this->validate($request, [
                'name' => 'sometimes',
                'email' => 'sometimes|unique:admins,email,'.$id,
                'password' => 'sometimes|confirmed',
                'status' => 'sometimes|boolean',
                'role_id' => 'sometimes|integer'
            ]);
            $password = $request->get('password');
            if (isset($password)) {
                $request->merge(['password' => bcrypt($password)]);
            }
            $admin = Admin::findOrFail($id);
            $admin->update(
                $request->only('name', 'email', 'password', 'role_id', 'status')
            );

            return $this->successResponse($admin, trans('core::app.response.update-success', ['name' => 'Admin']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    /**
     * Remove the specified  admin resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $admin = Admin::findOrFail($id);
            $admin->delete();
            return $this->successResponse(trans('core::app.response.deleted-success', ['name' => 'Admin']));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

}
