<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\PageAvailability;
use Modules\Page\Exceptions\AlreadyCreatedException;

class PageAvailabilityRepository extends BaseRepository
{
    private $pageAvailability;

    public function __construct(PageAvailability $pageAvailability)
    {
        $this->model = $pageAvailability;
        $this->model_key = "page-availability";
        $this->rules = [
            "page_id" => "required|numeric",
            "model_type" => "required",
            "model_id" => "required|numeric",
            "status" => "required|boolean"
        ];
    }

    public function allowedPageExist(array $check_data): bool
    {
        try
        {
            $exists = $this->model->where($check_data)->exists();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $exists;
    }

    public function checkClass(string $class_name): void
    {
        try
        {
            if (!class_exists($class_name)) throw ValidationException::withMessages([ "model_type" => "Requested model '{$class_name}' does not exist." ]);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function validateAllowData(array $data, ?array $merge = []): array
    {
        try
        {
            $this->checkClass($data["model_type"]);
            $validator = Validator::make($data, array_merge($this->rules, $merge));
            if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $validator->validated();
    }

    public function getBulkData(object $request, object $coupon): array
    {
        try
        {
            $allow_data = [];
            foreach ($request->all() as $data) {
                foreach ($data["model_id"] as $model_id) {
                    $allow_data[] = array_merge($this->validateAllowData([
                        "page_id" => $coupon->id,
                        "model_type" => $data["model_type"],
                        "model_id" => $model_id,
                        "status" => $data["status"]
                    ]));
                }
            }

            $filtered_data = array_filter($allow_data, function($data) {
                return !$this->allowedPageExist($data);
            });
            if ( count($filtered_data) == 0 ) throw new AlreadyCreatedException();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $filtered_data;
    }

    public function insertBulkData(array $data): bool
    {
        try
        {
            $inserted = $this->model::insert($data);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $inserted;
    }
}
