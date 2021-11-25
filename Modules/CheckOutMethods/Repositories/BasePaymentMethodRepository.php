<?php

namespace Modules\CheckOutMethods\Repositories;

use Exception;
use Modules\CheckOutMethods\Traits\HasPayementCalculation;

class BasePaymentMethodRepository 
{
    protected $payment_data, $encryptor;
    
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function all(): mixed
    {
        try
        {
            $data = [];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

}