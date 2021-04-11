<?php

namespace Modules\User\Transformers;

use Illuminate\Support\Carbon;
use Modules\User\Transformers\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            "name" => $this->full_name,
            "company" => $this->company,
            "address" => $this->address,
            "email" => $this->email,
            "status" => $this->status,
            "role" => $this->role ? new RoleResource($this->role) : null,
            "profile_image" => $this->profile_image_url,
            "avatar" => $this->avatar,
            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A')
        ];
    }
}
