<?php

namespace Modules\CheckOutMethods\Services;

use ArrayAccess;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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
