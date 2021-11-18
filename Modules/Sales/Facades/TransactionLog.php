<?php

namespace Modules\Sales\Facades;
use Illuminate\Support\Facades\Facade;

class TransactionLog extends Facade
{
    protected static function getFacadeAccessor() {
        return 'TransactionLog';
    }
}