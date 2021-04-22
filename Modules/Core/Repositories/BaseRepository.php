<?php

namespace Modules\Core\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
	protected $model, $model_key, $rules;

    public function model(): Model
    {
        return $this->model;
    }

    public function create(array $data, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created = $this->model->create($data);
			if ($callback) $callback($created);
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

    public function update(array $data, int $id, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();

			if ($callback) $callback($updated);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $updated);
        DB::commit();

        return $updated;
    }

    public function delete(int $id, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $deleted = $this->model->findOrFail($id);
			if ($callback) $callback($deleted);
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

    public function bulkDelete(object $request, ?callable $callback = null): object
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
			if ($callback) $callback($deleted);
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

    public function rules(array $merge = []): array
    {
        return array_merge($this->rules, $merge);
    }

    public function validateData(object $request, array $merge = [], ?callable $callback = null): array
    {
        $data = $request->validate($this->rules($merge));
		$append_data = $callback ? $callback($request) : [];

        return array_merge($data, $append_data);
    }
}