<?php

namespace  Modules\Product\Exceptions;

class ProductNotFoundIndividuallyException extends \Exception
{
	public function __construct()
	{
		parent::__construct(__("core::app.response.product-not-found-individually"));
	}
}