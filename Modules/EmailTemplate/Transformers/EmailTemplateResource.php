<?php

namespace Modules\EmailTemplate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "subject" => $this->subject,
            "content" => $this->content,
            "style" => $this->style,
            "is_system_defined" => (bool) $this->is_system_defined,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
