<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\User\Entities\Role;
    
/**
 * Admin user role controller
 */
class RoleController extends Controller
{
    public function __construct()
    {

    }


    public function index()
    {
        $roles = Role::all();
        return $roles;
    }

    public function show($id)
    {
       return Role::find($id);
    }

    public function store(Request $request)
    {
        try{
            Validator::make(request()->all(), [
                'name' => 'required',
                'permission_type' => 'required',
                'description' => 'required',
                'permissions' => 'required|json'
            ]);
            $params =  $request->all();
            $params =  array_merge($params, ['slug' => Str::slug($params['name'])]);
            $role = Role::create($params);
            return response()->json($role, 201);
        } catch (ValidationException $exception){
            return $exception->getMessage();
        } catch (ModelNotFoundException $exception){
            return $exception->getMessage();
        } catch (\Exception $exception){
            return $exception->getMessage();
        }

    }


    public function update(Request $request, $id)
    {
        try{
            $role = Role::find($id);
            Validator::make(request()->all(), [
                'name' => 'required',
                'permission_type' => 'required',
                'description' => 'required',
                'permissions' => 'required|json'
            ]);
            $params =  $request->all();
            $role = Role::create($params);
            return response()->json($role, 201);
        } catch (ValidationException $exception){
            return $exception->getMessage();
        } catch (ModelNotFoundException $exception){
            return $exception->getMessage();
        } catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

}