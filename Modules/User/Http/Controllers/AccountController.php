<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Entities\Admin;
use Illuminate\Http\JsonResponse;
use Modules\User\Transformers\AdminResource;
use Illuminate\Validation\ValidationException;
use Modules\User\Repositories\AdminRepository;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Exceptions\InvalidCredentialException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Account Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class AccountController extends BaseController
{

    protected $repository;

    public function __construct(Admin $admin, AdminRepository $adminRepository)
    {
        $this->model = $admin;
        $this->model_name = "Admin account";
        $this->repository = $adminRepository;

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Return currently logged in user's details
     * 
     * @return JsonResponse
     */
    public function show()
    {
        try
        {
            $fetched = auth()->guard('admin')->user();
        }
        catch (UnauthorizedHttpException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 401);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AdminResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try
        {
            $updated = auth()->guard('admin')->user();
            $data = $this->repository->validateData($request, $updated->id);

            $updated->fill($data);
            $updated->save();
        }
        catch (InvalidCredentialException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 401);
        }
        catch (ValidationException $exception)
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
     * Store profile image to current user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadProfileImage(Request $request)
    {
        try
        {
            $updated = auth()->guard('admin')->user();
            $this->repository->removeOldImage($updated->id);
            $updated = $this->repository->uploadProfileImage($request, $updated->id);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AdminResource($updated), "Profile image updated successfully.");
    }

    /**
     * Delete profile image from current user.
     *
     * @return JsonResponse
     */
    public function deleteProfileImage()
    {
        try
        {
            $updated = auth()->guard('admin')->user();
            $updated = $this->repository->removeOldImage($updated->id);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AdminResource($updated), "Image deleted successfully.");
    }
}
