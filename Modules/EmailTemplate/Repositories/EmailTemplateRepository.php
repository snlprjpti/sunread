<?php

namespace Modules\EmailTemplate\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateRepository extends BaseRepository
{
    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
        $this->model_key = "email_template";
        $this->rules = [
            "template_name" => "required",
            "template_subject" => "required",
            "template_content" => "required",
            "template_style" => "sometimes",
        ];
    }
}
