<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Role;


/**
 * Role controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RoleController extends BaseController
{
    protected $pagination_limit;

    /**
     * RoleController constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        $this->middleware('admin');
        parent::__construct();
    }

    /**
     * Returns all the roles
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            $payload = Role::paginate($this->pagination_limit);
            return $this->successResponse($payload);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(),400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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

            $payload = Role::findOrFail($id);
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new role
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, Role::rules());

            if(!$request->get('slug')){
                $request->merge(['slug' =>  Role::createSlug($request->get('name'))]);
            }
            $role = Role::create(
                $request->only('name' ,'description', 'permissions', 'permission_type', 'slug')
            );
            return $this->successResponse($payload = $role,trans('core::app.response.create-success', ['name' => 'Role']),201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        }catch (SlugCouldNotBeGenerated $exception){
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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
            $this->validate($request, Role::rules($id));

            $role = Role::findOrFail($id);
            $role = $role->update(
                $request->only('name' ,'description', 'permissions', 'permission_type')
            );

            return $this->successResponse($role,trans('core::app.response.update-success', ['name' => 'Role']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(),400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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

            $role = Role::findOrFail($id);
            $role->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Role']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(),404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}