<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;
use Modules\User\Repositories\AdminRepository;
use Modules\User\Transformers\AdminResource;
use Exception;

class UserInvitationController extends BaseController
{
    private $repository;

    public function __construct(Admin $admin, AdminRepository $adminRepository)
    {
        $this->middleware('guest:admin');

        $this->model = $admin;
        $this->model_name = "Admin account";

        parent::__construct($this->model, $this->model_name);
        $this->repository = $adminRepository;
    }

    public function resource(object $data): JsonResource
    {
        return new AdminResource($data);
    }

    public function getInvitationToken(Request $request)
    {
        try
        {
            $token = $request->token;
            if(!$token) throw new Exception(__("core::app.users.token.token-missing"));
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($token, $this->lang('fetch-success'));
    }

    public function acceptInvitation(Request $request): JsonResponse
    {
        try
        {
            $data = $request->validate([
                'invitation_token' => 'required',
                'password' => 'required|confirmed|min:6',
            ]);
            $user = $this->model->whereInvitationToken($request->invitation_token)->firstOrFail();

            $data = [
                "invitation_token" => null,
                "password" => Hash::make($data["password"])
            ];

            $updated = $this->repository->update($data, $user->id, function ($updated) {
                $updated->load("role");
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }
}
