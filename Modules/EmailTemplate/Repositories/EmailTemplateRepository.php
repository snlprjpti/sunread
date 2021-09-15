<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Support\Facades\Mail;
use Modules\Core\Repositories\BaseRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Modules\EmailTemplate\Entities\EmailVariable;
use Modules\EmailTemplate\Mail\SampleTemplate;

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

    public function sendEmailDemo(): void
    {
        $subject = 'view data';
        $template = EmailTemplate::findOrFail(2);
        preg_match_all("#\{\{(.*?)\}\}#", $template->template_content, $matches);

        if(count($matches[1]) > 0)
        {
            foreach($matches[1] as $match) {

                $value = EmailVariable::whereName($match)->pluck("value")->first();
                $template->template_content = str_ireplace("{{{$match}}}","{$value}", $template->template_content);
            }
        }

        $htmlBody = $template->template_content;
        $fromAddress = 'admin@gmail.com';
        Mail::to("sl.prjpti@gmail.com")->send(new SampleTemplate($subject, $htmlBody, $fromAddress));
    }

    public function validateTemplateContent(array $data)
    {
        $format = [];
        foreach ($data as $key => $value)
        {
            dd(is_int($value));
            if(is_int($value)) {
                dd("dasdasdasdasdasd");
                $id["id"] = $value;
                dd($id);
            }
            else {
                $content["content"] = $value;
            }
        }
        dd($format);
    }

    public function getTemplate(array $datas): string
    {
        $htmlTemplate = "";
        foreach($datas as $data)
        {
            if(is_int($data)) {
                $template = $this->model->find( $data ) ?? null;
                $htmlTemplate .= $template->template_content;
            }
            else {
                $htmlTemplate .= $data;
            }
        }
       return $htmlTemplate;
    }
}
