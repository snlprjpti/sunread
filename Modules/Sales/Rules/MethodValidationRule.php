<?php

namespace Modules\Sales\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Core\Facades\CoreCache;

class MethodValidationRule implements Rule
{
    protected $request, $attribute, $value;

    public function __construct(object $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd(config('sales.name'));
        dd($attribute, $value);
        // if ( $attribute ==  )
        CoreCache::getChannel($data["website"], $this->request->header("hc-channel"));
        //
    }

    public function message(): string
    {
        return "{$this->value} is not valid {$this->attribute}.";
    }
}
