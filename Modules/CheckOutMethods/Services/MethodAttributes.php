<?php

namespace Modules\CheckOutMethods\Services;

use ArrayAccess;

class MethodAttributes implements ArrayAccess
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }


}
