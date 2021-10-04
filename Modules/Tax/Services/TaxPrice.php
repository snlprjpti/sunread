<?php

namespace Modules\Tax\Services;

use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

class TaxPrice {


	public function getRate(object $request)
	{
		$website = CoreCache::getWebsite($request->header("hc-host"));
		dd($website);

		dd(SiteConfig::get());
		return ;
	}

}
