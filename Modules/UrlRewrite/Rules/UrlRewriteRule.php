<?php

namespace Modules\UrlRewrite\Rules;

use Illuminate\Contracts\Validation\Rule;

class UrlRewriteRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $type; 

    public function __construct($type)
    {
        $this->type = $type;
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
        $types = config("url-rewrite.path");

        foreach ($types as $key => $type) { 
            if (! class_exists($type) ) return false;
            if($this->type == $key ) return (bool) $type::whereId($value)->first();
        }
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "{$this->type}  is not found";
    }
}
