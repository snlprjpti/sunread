<?php

namespace Modules\EmailTemplate\Repositories;

use Illuminate\Support\Facades\Blade;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Store;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Repositories\ConfigurationRepository;
use Modules\Customer\Entities\Customer;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;
use Modules\EmailTemplate\Jobs\SendEmailJob;

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

    public function getConfigData(): array
    {
        try
        {
            $config_data = $this->config_template;
        }
        catch (Exception $exception)
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

            foreach ($elements as $element) {
                $parent = [];
                foreach ($element["variables"] as $variable) {
                    if (in_array($request->email_template_code, $variable["availability"]) || $variable["availability"] == ["all"]) {

                        unset($variable["availability"], $variable["source"], $variable["type"]);

                        $parent["label"] = $element["label"];
                        $parent["code"] = $element["code"];
                        $parent["variables"][] = $variable;
                    }
                }
                $data["groups"][] = $parent;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function templateGroupValidation(object $request): void
    {
        $all_groups = collect($this->config_template)->pluck("code")->toArray();
        if (!in_array($request->email_template_code, $all_groups)) throw ValidationException::withMessages(["email_template_code" => __("Invalid Template Code")]);
    }

    public function newEvent(string $event)
    {
        /*
         * get template from configurations
         * */
        $email_template_id = 3;
        $email_template = $this->model::findOrFail($email_template_id);

        /*
         *  get all variables according to template_codes
        */
//        $variables = $this->getEventVariable($email_template->email_template_code);


        /*
         *  get all variable with data
        */
        $variable_data = $this->getVariableData($email_template->email_template_code);


        $content = $this->render($email_template->content, $variable_data);
        $subject = $this->render($email_template->subject, $variable_data);
        $this->sendEmail($content, $subject);
    }

    public function render(string $content, $data = null): string
    {
        try
        {
            /*
             compile content to render in blade file
              */
            $php = Blade::compileString($content);
            ob_start();
            extract($data, EXTR_SKIP);
            eval('?' . '>' . $php);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return ob_get_clean();
    }

    public function sendEmail(string $content, string $subject): void
    {
        SendEmailJob::dispatch( $content, $subject );
    }

    public function getVariableData($template_code): array
    {
        try
        {
            $general = $this->getGeneralVariableData();
            switch ($template_code) {
                case "forgot_password" :
                    $data = $this->forgotPassword();
                    break;

                case "reset_password" :
                    $data = $this->resetPassword();
                    break;

                case "contact_form" :
                    $data = [];
                    break;

                case "new_account":
                case "welcome_email":
                    $data = $this->customerData();
                    break;

                case "new_order" :
                case "order_update" :
                case "new_guest_order" :
                case "order_update_guest" :
                    $data = $this->orderData();
                    break;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($general, $data);
    }

    public function getEventVariable(string $template_code): array
    {
        try
        {
            $elements = collect($this->config_variable)->pluck("variables")->flatten(1);

            $data = [];
            foreach ($elements as $element) {
                if (in_array($template_code, $element["availability"]) || $element["availability"] == ["all"]) {

                    $data[] = $element["variable"];
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function getCustomerData($customer_id)
    {
        try
        {
            $customer = Customer::findOrFail($customer_id);

            $store = Store::findOrFail($customer->store_id);
            $channel = $store->channel;

            $store_front_baseurl = SiteConfig::fetch("storefront_base_urL");

            $storefront_url = $store_front_baseurl . '/' . $channel->code . '/' . $store->code;

            $customer_dashboard_url = $storefront_url . '/account';

            $data = [
                "customer_id" => $customer->id,
                "customer_name" => $customer->first_name . ' ' . $customer->middle_name . ' ' . $customer->last_name,
                "customer_email_address" => $customer->email,
                "customer_dashboard_url" => $customer_dashboard_url,
                "account_confirmation_url" => $customer_dashboard_url,
                "password_reset_url" => $customer_dashboard_url,
                "order_id" => $customer_dashboard_url,
                "order_items" => $customer_dashboard_url,
                "billing_address" => $customer_dashboard_url,
                "shipping_address" => $customer_dashboard_url,
                "order" => $customer_dashboard_url
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function getGeneralVariableData(): array
    {
        try
        {
            /*
                get general variables data
            */
            $data = [
                "store_url" => SiteConfig::fetch("storefront_base_urL"),
                "store_name" => SiteConfig::fetch("store_name"),
                "store_phone_number" => SiteConfig::fetch("store_phone_number"),
                "store_country" => SiteConfig::fetch("store_country"),
                "store_state" => SiteConfig::fetch("store_region"),
                "store_post_code" => SiteConfig::fetch("store_zip_code"),
                "store_city" => SiteConfig::fetch("store_city"),
                "store_address_line_1" => SiteConfig::fetch("store_street_address"),
                "store_address_line_2" => SiteConfig::fetch("store_address_line2"),

                "store_vat_number" => SiteConfig::fetch("storefront_base_urL"),
                "store_email_address" => SiteConfig::fetch("storefront_base_urL"),
                "store_email_logo_url" => SiteConfig::fetch("storefront_base_urL"),
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    private function forgotPassword()
    {
        try
        {
            $customer_data = $this->getCustomerData(1);
            $data = [
                "password_reset_url" => "password_reset_url_link"
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($customer_data, $data);
    }

    private function resetPassword()
    {
        try
        {
            $customer_data = $this->getCustomerData(1);
            $data = [
                "password_reset_url" => "password_reset_url_link"
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($customer_data, $data);
    }

    private function customerData()
    {
        try
        {
            $customer_data = $this->getCustomerData(1);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $customer_data;
    }

    private function orderData()
    {
        try
        {
            $customer_data = $this->getCustomerData(1);
            $data = [
                "order_id" => 1,
                "order_items" => 1,
                "billing_address" => 1,
                "shipping_address" => 1,
                "order" => 1
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($customer_data, $data);
    }
}
