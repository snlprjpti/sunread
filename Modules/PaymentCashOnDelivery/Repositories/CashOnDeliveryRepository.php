<?php

namespace Modules\PaymentCashOnDelivery\Repositories;

use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;


class CashOnDeliveryRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
	protected object $request;
	protected object $parameter;
	protected string $method_key;

	public function __construct(object $request, object $parameter)
	{
		$this->request = $request;
		$this->method_key = "cash_on_delivey";

		parent::__construct($this->request, $this->method_key);
	}

	public function get(): mixed
	{
		
	}





	




}
