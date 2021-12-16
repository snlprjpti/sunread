<?php

namespace Modules\CheckOutMethods\Services;

use Exception;
use Illuminate\Support\Collection;
use Modules\Core\Facades\SiteConfig;

class BaseCheckOutMethods
{
    protected array $checkout_methods;
    protected array $method_attributes;
    protected mixed $check_out_method;
    protected bool $get_initial_repository;
    protected mixed $check_out_process_resolver;

    public function __construct(?string $check_out_method = null, ?bool $get_initial_repository = false)
    {
        $this->checkout_methods = ["delivery_methods", "payment_methods"];
        $this->check_out_method = isset($check_out_method) ?  $this->fetch($check_out_method) : $check_out_method;
        $this->get_initial_repository = $get_initial_repository;
        $this->check_out_process_resolver = CheckOutProcessResolver::class;
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
        $fetched = $this->all()->flatten(2)->where("slug", $method_slug)->first();
        if ($callback) $fetched = array_merge($fetched, $callback($fetched));
        if (!$fetched) throw new Exception("Could not fetch required attributes.");// exception for dev
        return $this->object($fetched);
    }

    public function process(object $request, ?array $parameter = [], ?object $method_data = null): mixed
    {
        if (!empty($parameter)) $parameter = $this->object($parameter);
        if ($this->check_out_method) $data = $this->getRepositoryData($this->check_out_method, $request, $parameter);
        elseif (isset($method_data)) $data = $this->getRepositoryData($method_data, $request, $parameter);
        else throw new Exception("Could not process method. Method is not initialize."); // Exception for dev
        return $data;
    }

    private function getRepositoryData(object $method_data, object $request, ?object $parameter): mixed
    {
        $resolver = $this->check_out_process_resolver;
        $resolver = new $resolver($request);
        if ($resolver->can_initilize($method_data->check_out_method)) return false;

        $method_repository = $method_data->repository;
        if (!class_exists($method_repository)) throw new Exception("Repository Path Not found.");
        $method_repository = new $method_repository($request, $parameter);
        if ($this->get_initial_repository) return $method_repository;
        $data = $method_repository->get();

        return $data;
    }

    private function getData(string $checkout_method, ?callable $callback = null): mixed
    {
        $proxy_method_data = [ [ "title" => "Proxy Checkout Method", "slug" => "proxy_checkout_method" ] ];
        return SiteConfig::get($checkout_method)->merge($proxy_method_data)->map(function ($method) use ($callback, $checkout_method) {
            $data = [
                "title" => $method["title"],
                "slug" => $method["slug"],
                "check_out_method" => $checkout_method,
                "repository" => array_key_exists("repository", $method) ? $method["repository"] : null
            ];
            if ($callback) $data = array_merge($data, $callback($method));
            return $data;	
        });
    }

}
