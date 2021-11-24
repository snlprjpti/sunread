<?php

namespace Modules\Repositories;

use Modules\CheckOutMethods\Traits\HasShippingCalculation;
use Modules\Core\Facades\SiteConfig;

class BaseDeliveryMethodRepository
{
	use HasShippingCalculation;

	protected string $shipping_method;
	protected string $shipping_method_path;
	protected mixed $method_configuration;

	public function __construct()
	{
		$this->method_configuration = SiteConfig::get("delivery_methods");
	}

	public function getList(): mixed
	{
		return SiteConfig::get("delivery_methods")->pluck("slug");
	}

	public function FunctionName(Type $var = null)
	{
		# code...
	}


}