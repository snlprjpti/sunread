<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;
use Modules\EmailTemplate\Mail\SampleTemplate;

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

    public function getTemplate(string $content, object $request): array
    {
        try
        {
            preg_match_all('/{{(.*?)}}/', $content, $preg_data);

            $fetched["templates"] = $this->findTemplateData($preg_data);

            $fetched["variables"] = $this->findVariableData($preg_data, $request);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
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

    public function findTemplateData(array $preg_data): array|null
    {
        try
        {
            $fetched = [];
            if(count($preg_data) > 0) {
                $temp = preg_grep("#\((.*?)\)#", $preg_data[0]);
                foreach($temp as $t) {
                    preg_match('#\("(.*?)"\)#', $t, $path);

                    $template = SiteConfig::fetch($path[1], "website", 1);
                    $fetched[$path[1]] = $template->content;
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function findVariableData(array $preg_data, object $request): array|null
    {
        try {
            $elements = collect($this->config_variable)->pluck("variables")->flatten(1);
            $fetched = [];
            if (count($preg_data) > 0) {
                $model_data = [];
                foreach ($preg_data[1] as $match) {
                    $element = $elements->where("variable", $match)->first();
                    if ($element) {
                        if ($element["source"] == "configuration") {
                            $request->request->add(['path' => $match]);

                            $value = $this->configurationRepository->getSinglePathValue($request);
                            $model_data[$match] = $value;
                        }
                        else {
                            $values = 1;
                            $a = $this->getProviderData($element, $values);
                            if ($element["column_type"] == "array") {
                                $name = "";
                                for ($i = 0; $i < count($element["column"]); $i++) {
                                    $name .= $a[$element["column"][$i]] ?? "";
                                    $name .= " ";
                                }
                                $model_data[$match] = $name;
                            }
                            else {
                                $model_data[$match] = $a[$element["column"]] ?? "";
                            }
                        }
                    }
                }
                $fetched = $model_data;
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getHtmlTemplate(string $content, object $request): string
    {
        try
        {
            preg_match_all('/{{(.*?)}}/', $content, $preg_data);

            $templates = $this->findTemplateData($preg_data);
            if(count($templates)>0) {
                $temp = preg_grep("#\((.*?)\)#", $preg_data[0]);

                foreach($temp as $t) {
                    preg_match('#\("(.*?)"\)#', $t, $path);

                    $content = str_replace($t, $templates[$path[1]], $content);
                }
            }

            $variables = $this->findVariableData($preg_data, $request);
            if(count($variables)>0) {

                $elements = collect($this->config_variable)->pluck("variables")->flatten(1);

                foreach ($preg_data[1] as $match) {

                    $element = $elements->where("variable", $match)->first();

                    if ($element) {

                        $content = str_replace("{{{$match}}}", $variables[$match], $content);
                    }
                }
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $content;
    }

    public function sendEmailDemo(object $request): void
    {
        $template = EmailTemplate::findOrFail(3);

        $content = $this->getHtmlTemplate($template->content, $request);
        $details = [
            'subject' => 'Sample Title From Mail',
            'body' => $content
        ];

        Mail::to("sl.prjpti@gmail.com")->send(new SampleTemplate($details));
    }

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
