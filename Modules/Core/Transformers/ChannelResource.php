<?php

namespace Modules\Core\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ChannelResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "code"=> $this->code,
            "name"=> $this->name,
            "description"=> $this->description,
            "hostname"=> $this->hostname,
            "default_locale"=> $this->default_locale,
            "base_currency"=> $this->base_currency,
            "logo_url" => $this->logo_url,
            "favicon_url" => $this->favicon_url
        ];
    }

}
