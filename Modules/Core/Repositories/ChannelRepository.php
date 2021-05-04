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
            "stores.*" => "sometimes|required|exists:stores,id",
            "default_store_id" => "required|exists:stores,id",
            "default_currency" => "required|exists:currencies,code",
            "website_id" => "required|exists:websites,id",

            /* General */
            "code" => "required|unique:channels,code",
            "hostname" => "required|unique:channels,hostname",
            "name" => "required",
            "description" => "required",
            "location" => "required",
            "timezone" => "required",

            /* Branding */
            "logo" => "required|mimes:bmp,jpeg,jpg,png,webp",
            "favicon" => "required|mimes:bmp,jpeg,jpg,png,webp",
            "theme" => "required|in:default"
        ];
    }
}
