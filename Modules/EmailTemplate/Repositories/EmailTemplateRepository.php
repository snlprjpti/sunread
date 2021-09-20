<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\BaseRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;

class EmailTemplateRepository extends BaseRepository
{
    protected $config_variable, $config_template;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
        $this->model_key = "email_template";
        $this->config_variable = config("email_variable");
        $this->config_template = config("email_template");

        $this->rules = [
            "name" => "required",
            "subject" => "required",
            "content" => "required",
            "email_template_code" => "required",
            "style" => "sometimes",
        ];
    }

    public function getConfigData(object $request): array
    {
        try
        {
            $config_data = $this->config_template;
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
            $config_data = $this->config_variable;

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
                    $data["groups"][] = $parent;
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data;
    }

    public function templateGroupValidation(object $reuest): void
    {
        $all_groups = collect($this->config_template)->pluck("code")->toArray();
        if(! in_array($reuest->email_template_code, $all_groups))  throw ValidationException::withMessages([ "email_template_code" => __("Invalid Template Code") ]);
    }

    public function getTemplate(string $content): string
    {
//        preg_match_all("#(?<={{)[^}]*(?=}})#", $content, $template);
//        preg_match_all("#(?<={{)[^}]*(?=}})#", $content, $variables);
//        preg_match_all("/{+(hc_include_template.*)}/", $content, $templates);
//dd($template);


        preg_match_all('/{{(.*?)}}/', $content, $data);
//        preg_match_all("#\{\{\s*(.*?)\s*\}\}#", $content, $variables);



        if(count($data) > 0) {
            $temp = preg_grep("#\((.*?)\)#", $data[0]);

            foreach($temp as $t) {
                preg_match('#\((.*?)\)#', $t, $path);

                $content = str_replace($t, $path[1], $content);
            }
        }

        if(count($data) > 0) {
            foreach($data[1] as $match) {
                $all_variables = $this->config_variable;

                foreach ($all_variables as $variable) {
                    foreach($variable as $value) {
                        if( in_array($match, array_column($value["variables"], "variable")))
                        {
                            $value = rand(1,100);
                            $content = str_replace("{{{$match}}}", $value, $content);
                        }
                    }
                }
            }
        }
//
//        $template = str_replace($variables[0],  $temp, $content);
//
//
//        $temp = [];
//        if(count($variables[1]) > 0)
//        {
//            foreach($variables[1] as $match) {
//
//                $template = EmailTemplate::whereSlug($slug)->first() ?? $match;
//                $temp = $template->content ?? $match;
//
////                $x[$slug] = strtr ($content, ["{include template={$slug}}" => $temp]);
//            }
//        }
//
//        $template = str_replace($matches[1],  $temp, $content);


        return $content;


//        $new  = str_replace($content, preg_match_all("#\{(.*?)\}#", $content, $matches), $temp[""]);
//
//        dd($new);
//
//        return $temp;
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
}
