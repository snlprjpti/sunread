<?php

namespace Modules\Core\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Contracts\StoreInterface;
use Modules\Core\Entities\Store;

class StoreRepository implements StoreInterface
{
    protected $model, $model_key;

    public function __construct(Store $store)
    {
        $this->model = $store;
        $this->model_key = "stores";
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function create(array $data): object
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

    public function update(array $data, int $id): object
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

    public function delete(int $id): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $deleted = $this->model->findOrFail($id);
            if ( $deleted->image !== null ) Storage::disk('public')->delete($deleted->image);
            
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

    public function bulkDelete(object $request): object
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

    public function rules(?int $id, array $merge = []): array
    {
        $id = $id ? ",{$id}" : null;
        $mimes = "bmp,jpeg,jpg,png,webp";
        $sometimes = $id ? "sometimes|nullable" : "required";

        return array_merge([
            "currency" => "required",
            "name" => "required",
            "slug" => "nullable|unique:stores,slug{$id}",
            "locale" => "required",
            "image" => "{$sometimes}|mimes:{$mimes}"
        ], $merge);
    }

    public function validateData(object $request, ?int $id=null, array $merge = []): array
    {
        $data = $request->validate($this->rules($id, $merge));
        if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->name);

        return $data;
    }
}
