<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class FQDN implements Rule
{

    public function __construct()
    {

    }

    public function passes($attribute, $value): bool
    {
        return preg_match("/^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$/", $value);
    }

    public function message(): string
    {
        return 'Invalid Hostname';
    }
}
