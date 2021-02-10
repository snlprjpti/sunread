<?php

namespace Modules\Core\Transformers;

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

            "subject" => isset($this->subject)? $this->subject->toArray():null,
            "causer" =>  isset($this->causer)? $this->causer->toArray():null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }

}
