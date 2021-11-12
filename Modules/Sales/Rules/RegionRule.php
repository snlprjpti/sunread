<?php

namespace Modules\Sales\Rules;

use Illuminate\Contracts\Validation\Rule;

class RegionRule implements Rule
{
    protected $address;

    public function __construct(array $address)
    {
        $this->address = $address;
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
        foreach ($this->address as $row) {
            if (!array_key_exists("region_id", $row) && !isset($row['region_name'])) 
            return false;            
            else continue;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'region name is required';
    }
}
