<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\App;
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
    private $channel_model, $store_model, $page_model;

    public function __construct(PageConfiguration $pageConfiguration, Channel $channel, Store $store, Page $page)
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
        $this->channel_model = $channel;
        $this->store_model = $store;
        $this->page_model = $page;
    }

    public function add(object $request): object
    {
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
            throw $exception;
        }

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

    public function getPageDetail(object $page): object
    {
        try
        {
            $result = null;
            if($page->scope != "website")
            {
                $data["page_id"] = $page->page_id;
                switch($page->scope)
                {
                    case "Modules\Core\Entities\Store":
                        $data["scope"] = "Modules\\Core\\Entities\\Channel";
                        $data["scope_id"] = $this->store_model->find($page->scope_id)->channel->id;
                        break;

                    case "Modules\Core\Entities\Channel":
                        $data["scope"] = "website";
                        $data["scope_id"] = $this->channel_model->find($page->scope_id)->website->id;
                        break;
                }
                $result = $this->checkCondition((object) $data)->first() ?? ($this->getPageDetail((object) $data));
            }
            if(!$result)
            {
                $result = $this->page($page);
            }

            return $result;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function page(object $data): object
    {
        try
        {
           return $this->page_model->whereId($data->page_id)->firstOrFail();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
