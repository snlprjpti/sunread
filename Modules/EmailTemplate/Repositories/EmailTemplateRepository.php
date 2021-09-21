<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;

class EmailTemplateRepository extends BaseRepository
{
    protected $config_variable, $config_template, $configurationRepository;

    public function __construct(EmailTemplate $emailTemplate, ConfigurationRepository $configurationRepository)
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
        $this->configurationRepository = $configurationRepository;
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
            $elements = collect($this->config_variable);

            foreach($elements as $element)
            {
                $parent = [];
                foreach($element["variables"] as $variable)
                {
                    if(in_array( $request->email_template_code, $variable["availability"]) || $variable["availability"] == ["all"]) {

                        unset($variable["availability"], $variable["source"], $variable["type"]);

                        $parent["label"] = $element["label"];
                        $parent["code"] = $element["code"];
                        $parent["variables"][] = $variable;
                    }
                }
                $data["groups"][] = $parent;
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data;
    }

    public function templateGroupValidation(object $request): void
    {
        $all_groups = collect($this->config_template)->pluck("code")->toArray();
        if(! in_array($request->email_template_code, $all_groups))  throw ValidationException::withMessages([ "email_template_code" => __("Invalid Template Code") ]);
    }

    public function getTemplate(string $content, object $request): string
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
        $elements = collect($this->config_variable)->pluck("variables")->flatten(1);


        if(count($data) > 0) {
            foreach($data[1] as $match) {

                $element = $elements->where("variable", $match)->first();
                if($element)
                {
                    if($element["source"] == "configuration")
                    {
                        $request->request->add(['path' => $match]);

                        $value = $this->configurationRepository->getSinglePathValue($request);
                        $content = str_replace("{{{$match}}}", $value, $content);
                    }
                    else
                    {
                        $values = 1;
                        $value = $this->getProviderData($element, $values);

                    }

                }
//                $all_variables = $this->config_variable;
//
//                foreach ($all_variables as $variable) {
//                    foreach($variable as $value) {
//
//                        if( in_array($match, array_column($value["variables"], "variable")))
//                        {
//                            $request->request->add(['path' => $match]);
//
//                            $value = $this->configurationRepository->getSinglePathValue($request);
//                            $content = str_replace("{{{$match}}}", $value, $content);
//                        }
//                    }
//                }
            }
        }

        return $content;
    }

    public function getProviderData(array $element, mixed $values): array
    {
        try
        {
            $model = new $element["source"];
            $fetched = is_array($values) ? $model->whereIn("id", $values)->get() : $model->find($values);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched->toArray();
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
