<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Entities\Admin;

class UserController extends BaseController
{
    public function index()
    {
        return $this->successResponse(200, $payload = Admin::all());
    }

    public function show($id)
    {
        return $this->successResponse(200, $payload = Admin::findOrFail($id));
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params,Admin::rules());
            if ($validator->fails()) {
                return $this->errorResponse(400, $validator->errors());
            }
            if (isset($params['password']) && $params['password']) {
                $params['password'] = bcrypt($params['password']);
            }
            $admin = Admin::create($params);
            return $this->successResponse(201, $admin, "Admin created Successfully");
        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $params = $request->all();

            $validator = Validator::make($params,Admin::rules($id));
            if ($validator->fails()) {
                return $this->errorResponse(400, $validator->errors());
            }

            if (isset($params['password']) && $params['password']) {
                $params['password'] = bcrypt($params['password']);
            }
            $admin = Admin::find($id);
            $admin = $admin->update($params);
            return $this->successResponse(200, $admin, "Admin updated Successfully");

        } catch (\Exception $exception) {
            $this->errorResponse(400, $exception->getMessage());
        }

    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try{
            $admin = Admin::find($id);
            $admin->delete();
            return $this->successResponse(400, null, "Admin deleted success");
        } catch (\Exception $exception){
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

}
