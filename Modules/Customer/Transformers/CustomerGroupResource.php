<?php

namespace Modules\Customer\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class CustomerGroupResource extends Resource
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
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "is_user_defined" => $this->is_user_defined,
            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A')
        ];
    }
}
