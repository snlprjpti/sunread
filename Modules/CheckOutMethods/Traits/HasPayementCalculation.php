<?php

namespace Modules\CheckOutMethods\Traits;

use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

trait HasPayementCalculation
{
	protected $payment_method;

	public function getPayemntMethods(): mixed
	{
		return SiteConfig::get("payment_methods");
	}

	public function getPaymentMethod(object $request, object $order, object $coreCache): mixed
	{
		$payment_method = $request->payment_method;
		dd("pay");
	}
}
