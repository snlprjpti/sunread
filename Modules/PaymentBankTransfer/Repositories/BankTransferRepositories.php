<?php

namespace Modules\PaymentMethods\Repositories;

use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;


class BankTransferRepositories extends BasePaymentMethodRepository
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
