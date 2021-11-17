<?php

namespace Modules\Sales\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;

class MethodValidationRule implements Rule
{
    protected $request, $attribute, $value, $config;

    public function __construct(object $request)
    {
        $this->request = $request;
        $this->config = config("sales");
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
        if ( $attribute ==  "shipping_method")
        {
            $shipping_methos = collect($this->config["shipping_methods"])->pluck("slug")->toArray();
            if (!in_array($value, $shipping_methos)) return false;
            $website = CoreCache::getWebsite($this->request->header("hc-host"));
            $channel = CoreCache::getChannel($website, $this->request->header("hc-channel"));
            dd($value);
            $value = SiteConfig::fetch($value, "channel", $channel->id);
            dd($value);

        }
        return true;
        // if ( $value )
    }

    public function message(): string
    {
        return "{$this->value} is not valid {$this->attribute}.";
    }
}
