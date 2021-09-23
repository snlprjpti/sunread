<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Store;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\Customer\Entities\Customer;
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

    public function newEvent()
    {
        $customer = Customer::findOrFail(1);

        $store = Store::findOrFail($customer->store_id);
        $channel = $store->channel;

        $store_front_baseurl = SiteConfig::fetch("storefront_base_urL");

        $storefront_url = $store_front_baseurl . '/' . $channel->code . '/' . $store->code;

        $customer_dashboard_url = $storefront_url . '/account';

        $data = [
            'customer_id' => $customer->id,
            'customer_name' => $customer->first_name.' '.$customer->middle_name.' '.$customer->last_name,
            'customer_email_address' => $customer->email,
            'customer_dashboard_url' => $customer_dashboard_url,
        ];

        $email_template_id = 3;

        $email_template = EmailTemplate::findOrFail( $email_template_id );

        $email_template->content = $this->render($email_template->content, $data);
        $email_template->subject = $this->render($email_template->subject, $data);
        $this->sendEmail($email_template);
    }

    public function render(string $content, $data = null): string
    {
        $php = Blade::compileString($content);
        ob_start();
        extract($data, EXTR_SKIP);
        eval('?' . '>' . $php);
        return ob_get_clean();
    }

    public function sendEmail(object $email_template): void
    {
        $details = [
            'body' => $email_template->content
        ];
        Mail::to("sl.prjpti@gmail.com")->send(new SampleTemplate($details, $email_template->subject));
    }
}
