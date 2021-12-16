<?php

namespace Modules\CheckOutMethods\Services;

use Exception;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

class CheckOutProcessResolver
{
	protected array $custom_shipment_handler, $custom_payment_handler;
	protected object $request;
	protected array $custom_checkout_handler_configuration;
	
	public function __construct(object $request)
	{
		$this->request = $request;
		$this->custom_shipment_handler = [ "klarna" ];
		$this->custom_payment_handler = [];
		$this->custom_checkout_handler_configuration = [
			"klarna" => "payment_methods_klarna_api_config_allow_custom_shipping_handler"
		];
	}

	public function is_checkout_disabled(string $checkout_method): bool
	{
		switch ($checkout_method) {
			case "delivery_methods": 
				$condition = $this->can_initilize("delivery_methods");
			break;

			case "payment_methods":
				$condition = false; // TODO::add condition if need to disable payment checkout
			break;
		}
        return $condition;
	}
	
	public function check(string $value, string $checkout_method): bool
	{
		$condition = true;
		if ($this->request->payment_method == "klarna" && $checkout_method == "delivery_methods") $condition = ($value == "proxy_checkout_method");
		return $condition;
	}

	public function can_initilize(string $checkout_method): bool
	{
		$condition = false;
		if (($this->request->payment_method == "klarna") && ($checkout_method == "delivery_methods")) $condition = $this->is_custom_checkout_enabled($this->custom_checkout_handler_configuration["klarna"]);
		return $condition;
	}

	public function allow_custom_checkout(): bool
	{
		return $this->is_custom_checkout_enabled($this->custom_checkout_handler_configuration["klarna"]);
	}

	public function is_custom_checkout_enabled(string $configuration_path): bool
	{
		$coreCache = $this->getCoreCache();
		$allow_custom_shipping_handler = SiteConfig::fetch($configuration_path, "channel", $coreCache->channel->id)?->pluck("iso_2_code")->toArray();
		$channel_country = SiteConfig::fetch("default_country", "channel", $coreCache->channel->id)?->iso_2_code;
		$condition = in_array($channel_country, $allow_custom_shipping_handler);
		return $condition;
	}

	public function getCheckOutMethods(?bool $filter_list = true, ?callable $callback = null): mixed
	{
		$coreCache = $this->getCoreCache();
		$method_lists = [];

		foreach( ["delivery_methods", "payment_methods"] as $check_out_method )
        {
            $get_method = SiteConfig::get($check_out_method);
			$get_method_list = $get_method->pluck("slug")->unique();

            foreach ($get_method_list as $key => $method) {
                $value = SiteConfig::fetch("{$check_out_method}_{$method}", "channel", $coreCache->channel?->id);
                if ($filter_list && !$value) continue;
            	$title = SiteConfig::fetch("{$check_out_method}_{$method}_title", "channel", $coreCache->channel?->id);
				$method_lists[$check_out_method][$key] = [
					"slug" => $method,
					"title" => $title,
					"visible" => true
				];
            }
			if ($callback) $method_lists[$check_out_method] = $callback($method_lists[$check_out_method], $check_out_method);
        }

		return $method_lists;
	}

	public function getCoreCache(): object
    {
        try
        {
            $data = [];
            if($this->request->header("hc-host")) $data["website"] = CoreCache::getWebsite($this->request->header("hc-host"));
            if($this->request->header("hc-channel")) $data["channel"] = CoreCache::getChannel($data["website"], $this->request->header("hc-channel"));
            if($this->request->header("hc-store")) $data["store"] = CoreCache::getStore($data["website"], $data["channel"], $this->request->header("hc-store"));
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return (object) $data;
    }


}