<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PageConfigurationRepository extends BaseRepository
{
    public function __construct(PageConfiguration $pageConfiguration)
    {
        $this->model = $pageConfiguration;
        $this->model_key = "page.configuration";
        $model_types_in = implode(",", config('page.model_config'));
        $this->rules = [
            "scope" => "required|in:{$model_types_in}",
            "scope_id" => "required|numeric",
            "page_id" => "required|numeric|exists:pages,id",
            "title" => "sometimes|nullable",
            "description" => "sometimes|nullable",
            "status" => "sometimes|boolean",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
        ];
    }

    public function add(object $request): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.add.before");

        try
        {
            $allow_data = array_merge($this->validateAllowData([
                "page_id" => $request->page_id,
                "scope" => $request->scope,
                "scope_id" => $request->scope_id,
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status,
                "meta_title" => $request->meta_title,
                "meta_description" => $request->meta_description,
                "meta_keywords" => $request->meta_keywords
            ]));

            if ($configData = $this->checkCondition((object) $allow_data)->first()) {
                $created = $this->update($allow_data, $configData->id);
            }else{
                $created = $this->create($allow_data);
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.add.after", $created);
        DB::commit();

        return $created;
    }

    public function validateAllowData(array $data, ?array $merge = []): array
    {
        try
        {
            $tableName = App::make($data["scope"])->getTable();
            $merge = [ "scope_id" => "required|numeric|exists:$tableName,id" ];
            $validator = Validator::make($data, array_merge($this->rules, $merge));
            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $validator->validated();
    }

    public function checkCondition(object $request): object
    {
        return $this->model->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['page_id', $request->page_id]
        ]);
    }
}
