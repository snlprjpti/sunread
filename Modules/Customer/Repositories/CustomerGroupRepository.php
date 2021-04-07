<?php

namespace Modules\Customer\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Modules\Customer\Entities\CustomerGroup;
use Modules\Customer\Contracts\CustomerGroupInterface;
use Modules\Customer\Exceptions\CustomersPresentInGroup;

class CustomerGroupRepository implements CustomerGroupInterface
{
    protected $model, $model_key;

    public function __construct(CustomerGroup $customer_group)
    {
        $this->model = $customer_group;
        $this->model_key = "customers.groups";
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
     * @param array $translation_data
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
            $deleted = $this->model->findOrFail($id);

            if ($deleted->customers()->count() > 0) throw new CustomersPresentInGroup("Customers are still present in group.");
            $deleted->delete();
        }
        catch (Exception $exception)
        {
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
            "name" => "required|min:2|max:100",
            "slug" => "nullable|unique:customer_groups,slug,{$id}"
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
        $data["is_user_defined"] = 1;
        if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

        return $data;
    }
}
