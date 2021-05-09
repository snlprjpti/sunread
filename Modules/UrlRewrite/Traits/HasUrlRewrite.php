<?php

namespace Modules\UrlRewrite\Traits;

trait HasUrlRewrite
{
	public function getUrlAttribute(): string
	{
		return "url";
	}
}