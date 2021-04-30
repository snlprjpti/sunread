<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class ConfigurationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $scope; 

    public function __construct($scope)
    {
        $this->scope = $scope;
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
        if($this->scope == "default") return ($value == 0);

        $websiteIds = Website::pluck("id")->toArray();
        if($this->scope == "website") return in_array($value, $websiteIds);

        $channelIds = Channel::pluck("id")->toArray();
        if($this->scope == "channel") return in_array($value, $channelIds);

        $storeIds = Store::pluck("id")->toArray();
        if($this->scope == "store") return in_array($value, $storeIds);
        
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
