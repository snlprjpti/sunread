<?php

namespace Modules\CheckOutMethods\Repositories;

use Exception;
use Illuminate\Support\Facades\Http;
use Modules\Core\Facades\CoreCache;

class BaseDeliveryMethodRepository
{
    protected object $request;
    protected string $method_key;
    protected string $user_name, $password;
    protected array $rules;

    public function __construct(object $request, string $method_key, ?array $rules = [])
    {
        $this->request = $request;
        $this->method_key = $method_key;
        $this->rules = $rules;
    }

    public function getCoreCache(): object
    {
        try
        {
            $data = [];
            if($this->request->header("hc-host")) $data["website"] = CoreCache::getWebsite($this->request->header("hc-host"));
            if($this->request->header("hc-channel")) $data["channel"] = CoreCache::getChannel($data["website"], $this->request->header("hc-channel"));
            if($this->request->header("hc-store")) $data["store"] = CoreCache::getStore($data["website"], $data["channel"], $this->request->header("hc-store"));
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return (object) $data;
    }

    public function basicAuth(string $user_name, string $password): object
    {
        return Http::withBasicAuth($user_name, $password);
    }

    public function rules(array $merge = []): array
    {
        return array_merge($this->rules, $merge);
    }

    public function validateData(object $request, array $merge = [], ?callable $callback = null): array
    {
        $data = $request->validate($this->rules($merge));
        $append_data = $callback ? $callback($request) : [];

        return array_merge($data, $append_data);
    }
}
