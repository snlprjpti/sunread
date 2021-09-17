<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Support\Arr;
use Modules\Core\Repositories\BaseRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;

class EmailTemplateRepository extends BaseRepository
{
    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
        $this->model_key = "email_template";
        $this->rules = [
            "name" => "required",
            "subject" => "required",
            "content" => "required",
            "style" => "sometimes",
        ];
    }

    public function getConfigData(object $request): array
    {
        try
        {
            $config_data = config("email_template");
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $config_data;
    }

    public function getConfigVariable(object $request): array
    {
        try
        {
            $config_data = config("email_variable");

            $data = [];

            foreach($config_data as $key=>$elements)
            {
                foreach($elements as $value)
                {
                    $parent = [];
                    foreach($value["variables"] as $v)
                    {
                        if(in_array( $request->template, $v["availability"]) || $v["availability"] == ["all"]) {

                            unset($v["availability"], $v["source"], $v["type"]);

                            $parent["label"] = $value["label"];
                            $parent["code"] = $value["code"];
                            $parent["variables"][] = $v;
                        }
                    }
                    $group["groups"][] = $parent;
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $group;
    }

//    public function sendEmailDemo(): void
//    {
//        $subject = 'view data';
//        $template = EmailTemplate::findOrFail(2);
//        preg_match_all("#\{\{(.*?)\}\}#", $template->template_content, $matches);
//
//        if(count($matches[1]) > 0)
//        {
//            foreach($matches[1] as $match) {
//
//                $value = EmailVariable::whereName($match)->pluck("value")->first();
//                $template->template_content = str_ireplace("{{{$match}}}","{$value}", $template->template_content);
//            }
//        }
//
//        $htmlBody = $template->template_content;
//        $fromAddress = 'admin@gmail.com';
//        Mail::to("sl.prjpti@gmail.com")->send(new SampleTemplate($subject, $htmlBody, $fromAddress));
//    }
//
//    public function validateTemplateContent(array $data)
//    {
//        $format = [];
//        foreach ($data as $key => $value)
//        {
//            dd(is_int($value));
//            if(is_int($value)) {
//                dd("dasdasdasdasdasd");
//                $id["id"] = $value;
//                dd($id);
//            }
//            else {
//                $content["content"] = $value;
//            }
//        }
//        dd($format);
//    }
//
//    public function getTemplate(string $content): string
//    {
//
////        $x = preg_replace("/^{include template/", 'x', $content);
//
////        dd($x);
//        preg_match_all("#\{(.*?)\}#", $content, $matches);
//
//        if(count($matches[1]) > 0)
//        {
//            foreach($matches[1] as $match) {
//                $slug = preg_replace('/^include template=/', '', $match);
//
//                $template = EmailTemplate::whereSlug($slug)->first() ?? $match;
//                $temp[] = $template->content ?? $match;
//
////                $x[$slug] = strtr ($content, ["{include template={$slug}}" => $temp]);
//            }
//        }
//
//        $template = str_replace($matches[1],  $temp, $content);
//
//        $template = str_replace(["{","}"],'',$template);
//
//        return $template;
//
//
//        $new  = str_replace($content, preg_match_all("#\{(.*?)\}#", $content, $matches), $temp[""]);
//
//        dd($new);
//
//        return $temp;
//    }
}
