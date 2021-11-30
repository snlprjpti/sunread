<?php

namespace Modules\PaymentKlarna\Repositories;

use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;


class KlarnaRepository extends BasePaymentMethodRepository
{
	public function __construct(object $request, object $parameter)
	{
		$this->method_key = "klarna";
		$this->request = $request;
		parent::__construct($this->request, $this->method_key);
	}

	public function get(): mixed
	{
		# code...
	}
}
