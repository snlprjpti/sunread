<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Entities\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Modules\User\Transformers\AdminResource;
use Illuminate\Validation\ValidationException;
use Modules\User\Repositories\AdminRepository;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Exceptions\CannotDeleteSelfException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\User\Exceptions\CannotDeleteSuperAdminException;

/**
 * User Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class UserController extends BaseController
{
    protected $repository;

    public function __construct(Admin $admin, AdminRepository $adminRepository)
    {
        $this->middleware('admin');
        $this->repository = $adminRepository;
        $this->model = $admin;
        $this->model_name = "Admin";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request  $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request, ["role"]);
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

        return $this->successResponse(AdminResource::collection($fetched), $this->lang('fetch-list-success'));
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
            $merge_rules = [
                "password" => "required|confirmed",
                "status" => "required|boolean",
                "role_id" => "required|integer|exists:roles,id"
            ];
            $data = $this->repository->validateData($request, null, $merge_rules);
            $created = $this->repository->create($data);
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

        return $this->successResponse(new AdminResource($created), $this->lang('create-success'), 201);
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
            $fetched = $this->model->with(["role"])->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AdminResource($fetched), $this->lang('fetch-success'));
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
            $merge_rules = [
                "password" => "required|confirmed",
                "status" => "required|boolean",
                "role_id" => "required|integer|exists:roles,id"
            ];
            $data = $this->repository->validateData($request, $id, $merge_rules, false);
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

        return $this->successResponse(new AdminResource($updated), $this->lang('update-success'));
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
        catch (CannotDeleteSelfException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (CannotDeleteSuperAdminException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
