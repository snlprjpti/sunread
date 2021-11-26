<?php

namespace Modules\CheckOutMethods\Repositories;

use Exception;
use Modules\Core\Facades\CoreCache;

class BaseDeliveryMethodRepository
{
	protected object $request;
	protected string $method_key;

	public function __construct(object $request, string $method_key)
	{
		$this->request = $request;
		$this->method_key = $method_key;
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
}