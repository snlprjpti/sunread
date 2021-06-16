<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Channel;
use Modules\Core\Repositories\BaseRepository;

class ChannelRepository extends BaseRepository
{
    public function __construct(Channel $channel)
    {
        $this->model = $channel;
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
}
