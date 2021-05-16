<?php

namespace Modules\UrlRewrite\Traits;

use Modules\UrlRewrite\Exceptions\UrlRewriteException;
use Modules\UrlRewrite\Facades\UrlRewrite;

trait HasUrlRewrite
{
    public array $urlRewriteParameter = ["id"];
    public array $urlRewriteExtraFields = [];
    public array $urlRewriteParameterKey = ["id"];

    public $urlRewriteRoute;
    public $urlRewriteRequestPath = "";

    public function getClass(): string
    {
        $name = explode("\\", get_class($this));
        return array_pop($name);
    }

	public function getUrlAttribute(): ?string
	{
        $urlRewrite = $this->getUrlRewrite();
		return $urlRewrite ? route('url.rewrite', $urlRewrite->request_path, false) : '';
	}

	public function getUrlRewrite(): ?object
    {
        if (!$this->urlRewriteRoute) throw new UrlRewriteException("Model {$this->getClass()} has not set route."); 
        return UrlRewrite::getByTypeAndAttributes(get_class($this), $this->getUrlRewriteAttributesArray());
    }

    public function getUrlRewriteAttributesArray(): ?array
    {
        $mapped = [];

        foreach ($this->urlRewriteParameter as $key => $attribute) {
            $mapped["parameter"][$this->urlRewriteParameterKey[$key]] = $this->getAttribute($attribute);
        }

        foreach ($this->urlRewriteExtraFields as $key => $attribute) {
            ($this->getAttribute($attribute) != null) ? $mapped["extra_fields"][$attribute] = $this->getAttribute($attribute) : false;
        }
        return $mapped;
    }
}