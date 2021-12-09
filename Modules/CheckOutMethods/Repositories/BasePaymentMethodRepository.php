<?php

namespace Modules\CheckOutMethods\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\CheckOutMethods\Exceptions\MethodException;
use Modules\CheckOutMethods\Services\MethodAttribute;
use Modules\CheckOutMethods\Traits\HasBasePaymentMethod;
use Modules\Core\Facades\CoreCache;
use Modules\Sales\Repositories\OrderMetaRepository;

class BasePaymentMethodRepository 
{
    use HasBasePaymentMethod;

    protected $payment_data, $encryptor;
    protected object $request;
    protected string $method_key;
    protected array $rules;

    public object $coreCache;
    public array $method_detail;
    public string $base_url;
    public array $headers; 
    public string $user_name, $password;
    public $orderRepository;
	public $orderMetaRepository;
    public $order;
	public mixed $base_data;


    public function __construct(object $request, string $method_key, ?array $rules = [])
    {
        $this->request = $request;
        $this->method_key = $method_key;
        $this->rules = $rules;
        $this->coreCache =  $this->getCoreCache();
        $this->method_detail = [
            "method_key" => $method_key
        ];
        $this->headers = [ "Accept" => "application/json" ];
        $this->orderMetaRepository = new CheckOutOrderMetaRepository();
        $this->orderRepository = new CheckOutOrderRepository();
    }

    public function object(array $attributes = []): mixed
    {
        return new MethodAttribute($attributes);
    }

    public function collection(array $attributes = []): Collection
    {
        return new Collection($attributes);
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

    public function methodDetail(): object
    {
        if (array_key_exists("user_name", $this->method_detail) && array_key_exists("password", $this->method_detail)) {
            $this->user_name = $this->method_detail["user_name"];
            $this->password = $this->method_detail["password"];
        } 
        $this->base_data = $this->object($this->method_detail);
        return $this->base_data;
    }

    public function basicAuth(string $user_name, string $password): object
    {
        return Http::withHeaders($this->headers)->withBasicAuth($user_name, $password);
    }

    public function getBasicClient(string $url, ?array $query = []): mixed
    {
        Event::dispatch("{$this->method_key}.get-basic-auth.before");
        
        try
        {
            $response = $this->basicAuth($this->user_name, $this->password)
            ->get("{$this->base_url}{$url}", $query)
            ->throw()
            ->json();
        }
        catch (Exception $exception )
        {
            throw new MethodException($exception->getMessage(), $exception->getCode());
        }

        Event::dispatch("{$this->method_key}.get-basic-auth", $response);
        return $response;
    }

    public function postBasicClient(string $url, array $data = []): mixed
    {
        Event::dispatch("{$this->method_key}.post-basic-auth.before");
        
        try
        {
            $response = $this->basicAuth($this->user_name, $this->password)
            ->post("{$this->base_url}{$url}", $data)
            ->throw()
            ->json();
         }
        catch (Exception $exception )
        {
            throw new MethodException($exception->getMessage(), $exception->getCode());
        }

        Event::dispatch("{$this->method_key}.post-basic-auth", $response);
        return $response;
    }

    public function putBasicClient(string $url, array $data = []): mixed
    {
        Event::dispatch("{$this->method_key}.put-basic-auth.before");
        
        try
        {
            $response = $this->basicAuth($this->user_name, $this->password)
            ->put("{$this->base_url}{$url}", $data)
            ->throw()
            ->json();
        }
        catch (Exception $exception )
        {
            throw new MethodException($exception->getMessage(), $exception->getCode());
        }

        Event::dispatch("{$this->method_key}.put-basic-auth", $response);
        return $response;
    }

    public function deleteBasicClient(string $url, array $data = []): mixed
    {
        Event::dispatch("{$this->method_key}.put-basic-auth.before");
        
        try
        {
            $response = $this->basicAuth($this->user_name, $this->password)
            ->delete("{$this->base_url}{$url}", $data)
            ->throw()
            ->json();
        }
        catch (Exception $exception )
        {
            throw new MethodException($exception->getMessage(), $exception->getCode());
        }

        Event::dispatch("{$this->method_key}.put-basic-auth", $response);
        return $response;
    }

    public function responseData(mixed $response): mixed
    {
        return $this->object(["response_data" => $response->json(), "response_status" => $response->status() ]);
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

    public function customValidate(array $data, array $rules, ?array $message = []): array
    {
        $validator = Validator::make($data, $rules, $message);
        if ( $validator->fails() ) throw ValidationException::withMessages($validator->errors()->toArray());
        return $validator->validated();
    }
}
