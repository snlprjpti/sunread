<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;
use Modules\User\Entities\Role;


/**
 * Role controller for the Admin
 * @author  Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RoleController extends BaseController
{
    protected $pagination_limit;

    protected $model_name = 'Role';

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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);

            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';

            $roles = Role::query();
            if ($request->has('q')) {
                $roles->whereLike(Role::$SEARCHABLE, $request->get('q'));
            }
            $roles = $roles->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $roles = $roles->paginate($limit);
            return $this->successResponse($roles, trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular role
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return $this->successResponse($role, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new role
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required',
                'permission_type' => 'required',
                'permissions' => 'nullable'
            ]);

            if (!$request->get('slug')) {
                $request->merge(['slug' => (new Role())->createSlug($request->get('name'))]);
            }
            $role = Role::create(
                $request->only('name', 'description', 'permissions', 'permission_type', 'slug')
            );
            return $this->successResponse($payload = $role, trans('core::app.response.create-success', ['name' => 'Role']), 201);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Updates the role details
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'sometimes',
                'permission_type' => 'required',
                'permissions' => 'required'
            ]);

            $role = Role::findOrFail($id);
            $role = $role->forceFill(
                $request->only('name', 'description', 'permissions', 'permission_type')
            );
            $role->save();

            return $this->successResponse($role, trans('core::app.response.update-success', ['name' => 'Role']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular role
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $doesUserWithRoleExist = Admin::where('role_id', $role->id)->exists();
            if ($doesUserWithRoleExist) {
                return $this->errorResponse("User with the given role exist.", 403);
            }
            if ($doesUserWithRoleExist) {
                return $this->errorResponse("Super Admin cannot be deleted.", 403);
            }

            $role->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Role']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);;

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    public function fetchPermission(Request $request)
    {
        try{
            $permissions = config('acl');
            dd($permissions);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);;

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}
