<?php

namespace Modules\UrlRewrite\Traits;

use Modules\UrlRewrite\Facades\UrlRewrite;

trait HasUrlRewrite
{
	public function getUrlAttribute(): string
	{
		if (! $urlRewrite = $this->getUrlRewrite()) return '';
		return route('url.rewrite', $urlRewrite->request_path, false);
	}

	public function getUrlRewrite(): ?object
    {
        return UrlRewrite::getByTypeAndAttributes(config("url-rewrite.types.{$this->urlRewriteType}.route"), $this->getUrlRewriteAttributesArray());
    }

	public function getUrlRewriteAttributesArray(): ?array
    {
        $mapped = [];
        $base_config_key = "url-rewrite.types.{$this->urlRewriteType}.attributes";

        foreach (config("{$base_config_key}.parameter") as $key => $attribute) {
            $mapped['parameter'][config("{$base_config_key}.parameter_key.$key")] = $this->getAttribute($attribute);
        }

        foreach (config("{$base_config_key}.extra_fields") as $key => $attribute) {
            ($this->getAttribute($attribute) != null) ? $mapped['extra_fields'][$attribute] = $this->getAttribute($attribute) : false;
        }
        
        return $mapped;
    }
}