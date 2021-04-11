<?php

namespace Modules\User\Http\Controllers;

use Modules\Core\Tree;
use Illuminate\Http\Request;
use Modules\User\Entities\Role;
use Modules\User\Entities\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Modules\User\Transformers\RoleResource;
use Modules\User\Repositories\RoleRepository;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Exceptions\RoleHasAdminsException;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Role controller for the Admin
 * @author  Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class RoleController extends BaseController
{
    protected $repository;

    public function __construct(Role $role, RoleRepository $roleRepository)
    {
        $this->middleware('admin');
        $this->repository = $roleRepository;
        $this->model = $role;
        $this->model_name = "Role";

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(RoleResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Stores new resource
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $this->repository->validateData($request);
            if ( $request->permission_type == "custom" ) $this->repository->checkPermissionExists($request->permissions);

            $created = $this->repository->create($data);
        }
        catch (SlugCouldNotBeGenerated $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new RoleResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular resource
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $fetched = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new RoleResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Fetch Permissions
     * 
     * @return JsonResponse
     */
    public function fetchPermission()
    {
        try
        {
            $acl = $this->createACL();
            $this->model_name = "Permission";
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse($acl->items, $this->lang('fetch-success'));
    }



    /**
     * Updates the resource
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {
            $data = $this->repository->validateData($request, $id);
            $updated = $this->repository->update($data, $id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new RoleResource($updated), $this->lang('update-success'));
    }

    /**
     * Remove the specified  admin resource from storage.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $this->repository->delete($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (RoleHasAdminsException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    /**
     * Create ACL tree
     * 
     * @return Tree
     */
    public function createACL()
    {
        $tree = Tree::create();
        foreach (config('acl') as $item) {
            $tree->add($item, 'acl');
        }
        return $tree;
    }
}
