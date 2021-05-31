<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->full_name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "company" => $this->company,
            "address" => $this->address,
            "email" => $this->email,
            "status" => $this->status,
            "role" => $this->role ? new RoleResource($this->role) : null,
            "profile_image" => $this->profile_image_url,
            "avatar" => $this->avatar,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
