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
        $this->attribute = $attribute;
        $this->value = $value;
        if ( $attribute ==  "shipping_method" ) return $this->check($value, "delivery_methods");
        elseif ( $attribute ==  "payment_method" ) return $this->check($value, "payment_methods");
        return true;
    }

    public function message(): string
    {
        return "{$this->value} is not valid {$this->attribute}.";
    }

    public function check(string $value, string $method): bool
    {
        $methods = SiteConfig::get($method);
        $check_methods = $methods->pluck("slug")->toArray();
        if (!in_array($value, $check_methods)) return false;
        $website = CoreCache::getWebsite($this->request->header("hc-host"));
        $channel = CoreCache::getChannel($website, $this->request->header("hc-channel"));
        $value = SiteConfig::fetch("{$method}_{$value}", "channel", $channel->id);
        if ($value) return true;
        return false;
    }
}
