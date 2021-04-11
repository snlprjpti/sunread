<?php

namespace Modules\User\Repositories;

use Exception;
use Modules\User\Entities\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Modules\User\Contracts\RoleInterface;
use Illuminate\Validation\ValidationException;
use Modules\User\Exceptions\CannotDeleteSelfException;
use Modules\User\Exceptions\InvalidCredentialException;
use Modules\User\Exceptions\RoleHasAdminsException;

class RoleRepository implements RoleInterface
{
    protected $model, $model_key;

    public function __construct(Role $role)
    {
        $this->model = $role;
        $this->model_key = "role";
    }

    /**
     * Get current Model
     * 
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Create a new resource
     * 
     * @param array $data
     * @return Model
     */
    public function create($data)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created = $this->model->create($data);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
        DB::commit();

        return $created;
    }

    /**
     * Update requested resource
     * 
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update($data, $id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $updated);
        DB::commit();

        return $updated;
    }

    /**
     * Delete requested resource
     * 
     * @param int $id
     * @return Model
     */
    public function delete($id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $deleted = $this->model->withCount("admins")->findOrFail($id);
            if ( $deleted->admins_count > 0 ) throw new RoleHasAdminsException("Admins are present with this role.");
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Delete requested resources in bulk
     * 
     * @param Request $request
     * @return Model
     */
    public function bulkDelete($request)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $request->validate([
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs,id',
            ]);

            $deleted = $this->model->whereIn('id', $request->ids);
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Returns validation rules
     * 
     * @param int $id
     * @param array $merge
     * @return array
     */
    public function rules($id, $merge = [])
    {
        $id = $id ? ",{$id}" : null;

        return array_merge([
            "name" => "required",
            "slug" => "nullable|unique:roles,slug{$id}",
            "description" => "nullable",
            "permission_type" => "required|in:all,custom",
            "permissions" => "sometimes"
        ], $merge);
    }

    /**
     * Validates form request
     * 
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function validateData($request, $id=null, $merge = [])
    {
        $data = $request->validate($this->rules($id, $merge));
        if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);
        if ( $request->permission_type != "custom" ) $data["permissions"] = [];

        return $data;
    }

    /**
     * Check if permission exists in our config
     * 
     * @throws ValidationException
     */
    public function checkPermissionExists($permissions)
    {
        $all_permissions = array_column(config("acl"), "key");
        if(array_diff($permissions, $all_permissions)){
            throw ValidationException::withMessages([
               "permissions" => "Invalids permissions."
            ]);
        };
    }
}
