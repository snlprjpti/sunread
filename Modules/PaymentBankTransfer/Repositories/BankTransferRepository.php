<?php

namespace Modules\PaymentMethods\Repositories;

use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;


class BankTransferRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
	protected object $request;
	protected object $parameter;
	protected string $method_key;

	public function __construct(object $request, object $parameter)
	{
		$this->request = $request;
		$this->method_key = "bank_transfer";
     
		parent::__construct($this->request, $this->method_key);
	}

	public function get(): mixed
	{
		
	}

	




}
