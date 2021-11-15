<?php

namespace Modules\Sales\Rules;

use Illuminate\Contracts\Validation\Rule;

class RegionRule implements Rule
{
    protected $address, $name;

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
            if (!array_key_exists("region_id", $row) && !isset($row['region_name'])) {
                $this->name =  "region name";
                return false;
            } elseif (!array_key_exists("city_id", $row) && !isset($row['city_name'])) {
                $this->name =  "city name";
                return false;
            } else { 
                continue;
            }
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
        return "{$this->name} is required";
    }
}
