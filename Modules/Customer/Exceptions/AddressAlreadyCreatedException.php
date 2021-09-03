<?php

namespace Modules\Customer\Exceptions;

class AddressAlreadyCreatedException extends  \Exception
{
    public function __construct()
    {
        parent::__construct("Address Already Added");
    }
}
