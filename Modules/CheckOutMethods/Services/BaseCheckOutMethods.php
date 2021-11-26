<?php

namespace Modules\CheckOutMethods\Services;

use Illuminate\Support\Collection;
use Modules\Core\Facades\SiteConfig;

class BaseCheckOutMethods
{

	protected array $checkout_methods;

	protected array $method_attributes;

	public function __construct()
	{
		$this->checkout_methods = ["delivery_methods", "payment_methods"];
	}

	public function object(array $attributes = []): mixed
	{
		return new MethodAttribute($attributes);
	}

	public function collection(array $attributes = []): Collection
	{
		return new Collection($attributes);
	}

	public function all(?callable $callback = null): mixed
	{
		$check_out_methods = $this->collection($this->checkout_methods);
		return $check_out_methods->map( function ($check_out_method) use ($callback) {
			return [
				$check_out_method => $this->getData($check_out_method, $callback)->toArray()
			];
		});
	}

	public function get(?string $method_name = "delivery_methods", ?callable $callback = null): mixed
	{
		return $this->getData($method_name, $callback);
	}

	public function fetch(string $method_slug, ?callable $callback = null): mixed
	{
		return config("sales");
		$fetched = $this->all()->flatten(2)->where("slug", $method_slug)->first();
		if ($callback) $fetched = array_merge($fetched, $callback($fetched));
		return $this->object($fetched);
	}

	private function getData(string $checkout_method, ?callable $callback = null): mixed
	{
		return SiteConfig::get($checkout_method)->map(function ($method) use ($callback) {
			$data = [
				"title" => $method["title"],
				"slug" => $method["slug"],
				"repository" => array_key_exists("repository", $method) ? $method["repository"] : null
			];
			if ($callback) $data = array_merge($data, $callback($method));
			return $data;	
		});
	}

}
