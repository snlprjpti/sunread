<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    protected $model_name = 'Admin';
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request  $request)
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

            $admins =  Admin::query();
            if ($request->has('q')) {
                $admins->whereLike(Admin::$SEARCHABLE,$request->get('q'));
            }
            $admins = $admins->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit')? $request->get('limit'):$this->pagination_limit;
            $admins = $admins->paginate($limit);
            return $this->successResponse($admins, trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Get the particular admin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $admin = Admin::findOrFail($id);
            return $this->successResponse($admin, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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
            $this->validate($request, [
                'first_name' => 'required|min:2|max:200',
                'last_name' => 'required|min:2|max:200',
                'company' =>'required|min:3|max:200',
                'address' =>'required|min:3|max:200',
                'email' => 'required|unique:admins,email',
                'password' => 'required|confirmed',
                'status' => 'required|boolean',
                'role_id' => 'required|integer'
            ]);

            $password = $request->get('password');
            if (isset($password)) {
                $request->merge(['password' => bcrypt($password)]);
            }

            $admin = Admin::create(
                $request->only('first_name','last_name','address','company', 'email', 'password', 'role_id', 'status')
            );

            return $this->successResponse($admin, trans('core::app.response.create-success', ['name' => $this->model_name]), 201);

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
                'first_name' => 'sometimes|min:2|max:200',
                'last_name' => 'sometimes|min:2|max:200',
                'company' =>'sometimes|min:3|max:200',
                'address' =>'sometimes|min:3|max:200',
                'email' => 'sometimes|unique:admins,email,'.$id,
                'password' => 'sometimes|confirmed',
                'status' => 'sometimes|boolean',
                'role_id' => 'sometimes|integer',
            ]);
            $password = $request->get('password');
            if (isset($password)) {
                $request->merge(['password' => bcrypt($password)]);
            }
            $admin = Admin::findOrFail($id);
            $admin->forceFill(
                $request->only('first_name','last_name','address','company', 'email', 'password', 'role_id', 'status')
            );
            $admin->save();
            return $this->successResponse($admin, trans('core::app.response.update-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

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
            $current_user = Auth::guard('admin')->user();

            if($admin->id === $current_user->id){
                return $this->errorResponse("Admin cannot delete itself.",403);
            }
            if($admin->hasRole('super-admin')){
                return $this->errorResponse("Super Admin cannot be deleted." ,403);
            }
            $admin->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => $this->model_name]));

        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

}
