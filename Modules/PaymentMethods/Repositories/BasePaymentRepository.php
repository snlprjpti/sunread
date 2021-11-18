<?php

namespace Modules\PaymentMethods\Repositories;

use Modules\PaymentMethods\Traits\HasPayementCalculation;

class BasePaymentRepository
{
	use HasPayementCalculation;
	
	protected array $rules;
	protected string $method_key;

	public function __construct(string $method_key, array $rules = [])
	{
		$this->method_key = $method_key;
		$this->rules = $rules;
	}




}
