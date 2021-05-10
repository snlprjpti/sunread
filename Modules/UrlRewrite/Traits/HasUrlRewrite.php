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

        foreach (config("url-rewrite.types.{$this->urlRewriteType}.attributes") as $key => $attribute) {
            $mapped[config("url-rewrite.types.{$this->urlRewriteType}.parameter.$key")] = $this->getAttribute($attribute);
        }

        return $mapped;
    }
}