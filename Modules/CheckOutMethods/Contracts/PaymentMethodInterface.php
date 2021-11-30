<?php

namespace Modules\CheckOutMethods\Contracts;

interface PaymentMethodInterface
{
	public function get(): mixed;
}
