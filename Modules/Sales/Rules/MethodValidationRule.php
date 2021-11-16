<?php

namespace Modules\Sales\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

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
        dd(config('sales.shipping_methods'));
        
        dd($attribute, $value);
        // if ( $attribute ==  "shipping_method")
        {
            $channel = CoreCache::getChannel($value, $this->request->header("hc-channel"));
            $value = SiteConfig::fetch($value, "channel", $channel->id);
        }

        // if ( $value )
    }

    public function message(): string
    {
        return "{$this->value} is not valid {$this->attribute}.";
    }
}
