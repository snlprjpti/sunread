<?php

namespace Modules\Category\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class ScopeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $scope; 

    public function __construct($data)
    {
        $this->data = $data;
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
        if($this->data->scope == "website") return (bool) Website::whereId($value)->first();

        if($this->data->scope == "channel")  return (bool) Channel::whereId($value)->first() && in_array($value, Website::find($this->data->website_id)->channels->pluck('id')->toArray());

        if($this->data->scope == "store")  return (bool) Store::whereId($value)->first();
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Scope Id doesnt exists';
    }
}
