<?php

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Modules\UrlRewrite\Contracts\UrlRewriteInterface;

class CachingUrlRewriteRepository implements UrlRewriteInterface
{
	public const URL_REWRITE_ALL = 'url_rewrite_all';

	public const URL_REWRITE_ID = 'url_rewrite_id_';

	public const URL_REWRITE_REQUEST_PATH = 'url_rewrite_request_path_';

	public const URL_REWRITE_TARGET_PATH = 'url_rewrite_target_path_';

	public const URL_REWRITE_TYPE_ATTRIBUTES = 'url_rewrite_type_attributes_';

	protected $repository, $cache;

	public function __construct(UrlRewriteInterface $repository, Cache $cache)
	{
		$this->repository = $repository;
		$this->cache = $cache;
		$this->addTagIfPossible();
	}

	protected function remember(string $key, string $method, ...$arguments)
	{
		return $this->cache->remember($key, $this->getTtl(), function() use ($method, $arguments){
			return $this->repository->{$method}(...$arguments);
		});
	}
	
	protected function addTagIfPossible(): void
	{
		if ($this->cache->getStore() instanceof TaggableStore) {
			$this->cache = $this->cache->tags(config("url-rewrite.cache-key"));
		}
	}
}