<?php

namespace Modules\Core\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            "log_name" => $this->log_name,
            "description" => $this->description,
            "action" => $this->action,
            "activity" => $this->activity,
            "subject" => $this->whenLoaded("subject"),
            "causer" => $this->whenLoaded("causer"),
            "properties" => $this->properties,
            "created_at" =>  Carbon::parse($this->created_at)->format('M j\\,Y H:i A'),
        ];
    }

}
