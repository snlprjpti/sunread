<?php

namespace Modules\EmailTemplate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "template_name" => $this->template_name,
            "template_subject" => $this->template_subject,
            "template_content" => $this->template_content,
            "template_style" => $this->template_style,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
