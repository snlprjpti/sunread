<?php

namespace Modules\PaymentMethods\Repositories;


class PaymentBaseRepository extends BasePaymentRepository
{
	public function __construct()
	{
		$this->method_key = "bank_transfer";
		$this->rules = [
			"bank_transfer" => "required"
		];

		parent::__construct($this->method_key, $this->rules);
	}

	




}
