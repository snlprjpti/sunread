<?php

namespace Modules\Core\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ActivityResource extends Resource
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
            "subject" => $this->subject ? $this->subject->toArray() : null,
            "causer" => $this->causer ? $this->causer->toArray() : null,
            "properties" => $this->properties,
            "created_at" =>  Carbon::parse($this->created_at)->format('M j\\,Y H:i A'),
        ];
    }

}
