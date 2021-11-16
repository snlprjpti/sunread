<?php

namespace Modules\Sales\Traits;

use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

trait HasPayementCalculation
{
	protected $payment_method;

	public function getPayemntMethod(object $request, string $payment_method)
	{
		// SiteConfig::fetch("")
	}

	public function FunctionName(Type $var = null)
	{
		# code...
	}
}
