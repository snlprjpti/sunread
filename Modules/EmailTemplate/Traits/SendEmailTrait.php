<?php

namespace Modules\EmailTemplate\Traits;

use Illuminate\Support\Facades\Blade;
use Modules\Core\Entities\Store;
use Modules\Core\Facades\SiteConfig;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Exception;

trait SendEmailTrait
{
    public function newEvent(string $event, int $entity_id): array
    {
        try
        {
            $entity_id = 1;
            /*
             * get template from configurations according to scope, scope id and event code
             */
            $email_template_id = 1;
            $email_template = EmailTemplate::findOrFail($email_template_id);

            /*
             *  get all variables according to template_codes
            */
//        $variables = $this->getEventVariable($email_template->email_template_code);

            /*
             *  get all variable with data
            */
            $variable_data = $this->getVariableData($email_template->email_template_code, $entity_id);

            $data["content"] = $this->render($email_template->content, $variable_data);
            $data["subject"] = $this->render($email_template->subject, $variable_data);
            $data["to_email"] = $variable_data["customer_email_address"];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
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

    public function getVariableData(string $event_code, int $entity_id): array
    {
        try
        {
            $general = $this->getGeneralVariableData();
            switch ($event_code) {
                case "forgot_password" :
                    $data = $this->forgotPassword($entity_id);
                    break;

                case "reset_password" :
                    $data = $this->resetPassword($entity_id);
                    break;

                case "contact_form" :
                    $data = [];
                    break;

                case "new_account":
                case "welcome_email":
                    $data = $this->getCustomerData($entity_id);
                    break;

                case "new_order" :
                case "order_update" :
                case "new_guest_order" :
                case "order_update_guest" :
                    $data = $this->orderData($entity_id);
                    break;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($general, $data);
    }

    private function getCustomerData(int $customer_id)
    {
        try
        {
            /* get customer data by customer id */
            $customer = Customer::findOrFail($customer_id);
            /* get store data by its id */
            $store = Store::findOrFail($customer->store_id);
            /* get channel by store */
            $channel = $store->channel;

            /* get store url from configuration */
            $store_front_baseurl = SiteConfig::fetch("storefront_base_urL", "store", $store->id);

            $storefront_url = $store_front_baseurl . '/' . $channel->code . '/' . $store->code;

            $customer_dashboard_url = $storefront_url . '/account';

            $data = [
                "customer_id" => $customer->id,
                "customer_name" => $customer->first_name . ' ' . $customer->middle_name . ' ' . $customer->last_name,
                "customer_email_address" => $customer->email,
                "customer_dashboard_url" => $customer_dashboard_url,
                "account_confirmation_url" => $customer_dashboard_url
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    /*
        get general variables data
    */
    public function getGeneralVariableData(): array
    {
        try
        {
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

    /*
        get forgot password variables data
    */
    private function forgotPassword(int $customer_id)
    {
        try
        {
            $customer_data = $this->getCustomerData($customer_id);
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

    /*
        get reset password variables data
    */
    private function resetPassword(int $customer_id)
    {
        try
        {
            $customer_data = $this->getCustomerData($customer_id);
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

    /*
        get order variables data
    */
    private function orderData(int $entity_id)
    {
        try
        {
            /* get order object by its entity id */
            $order = $entity_id;
            $customer_id = 1;
            $customer_data = $this->getCustomerData($customer_id);
            $billing = $this->getBillingAddress($customer_id);
            $shipping = $this->getShippingAddress($customer_id);
            $data = [
                "order_id" => 1,
                "order_items" => 1,
                "billing_address" => $billing,
                "shipping_address" => $shipping,
                "order" => 1
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return array_merge($customer_data, $data);
    }

    /*
        get customer billing address data
    */
    private function getBillingAddress(int $customer_id): object|null
    {
        try
        {
            $address = CustomerAddress::whereCustomerId($customer_id)->whereDefaultBillingAddress(1)->first();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $address;
    }

    /*
        get customer shipping address data
    */
    private function getShippingAddress(int $customer_id): object|null
    {
        try
        {
            $address = CustomerAddress::whereCustomerId($customer_id)->whereDefaultShippingAddress(1)->first();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $address;
    }
}
