<?php

namespace Modules\UrlRewrite\Contracts;

/**
 * Url Rewrite Interface
 */
interface UrlRewriteInterface
{
	public function getModel(): object;

    public function setModel(object $model): object;

	public function getByRequestPath(string $url): ?object;
}