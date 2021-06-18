<?php

namespace Modules\Core\Repositories;

use Exception;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;

class ChannelRepository extends BaseRepository
{
    protected $store;

    public function __construct(Channel $channel, Store $store)
    {
        $this->model = $channel;
        $this->store = $store;
        $this->model_key = "core.channel";
        $this->rules = [
            /* Foreign Keys */
            "default_store_id" => "nullable|exists:stores,id",
            "default_currency" => "nullable|exists:currencies,code",
            "website_id" => "required|exists:websites,id",
            "default_category_id" => "nullable|exists:categories,id",

            /* General */
            "code" => "required|unique:channels,code",
            "hostname" => "nullable|unique:channels,hostname",
            "name" => "required",
            "description" => "required",
            "location" => "nullable",
            "timezone" => "nullable",
            "status" => "sometimes|boolean",

            /* Branding */
            "logo" => "nullable|mimes:bmp,jpeg,jpg,png,webp",
            "favicon" => "nullable|mimes:bmp,jpeg,jpg,png,webp",
            "theme" => "nullable|in:default"
        ];
    }

    public function defaultStoreValidation(array $data, int $id): void
    {
        try
        {
            if($this->store->find($data['default_store_id'])->channel->id != $id)
            throw ValidationException::withMessages([ "default_store_id" =>  __("core::app.response.store_does_not_belong", ["name" => $data['name']]) ]);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }     
    }
}
