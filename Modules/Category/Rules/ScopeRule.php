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
    public $data, $website_model; 

    public function __construct($data)
    {
        $this->data = $data;
        $this->website_model = new Website();
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
        if($this->data->category_id) $category = Category::find($this->data->category_id);
        if(isset($category)) $website_id = $category->website_id;

        if($this->data->website_id) $website_id = $this->data->website_id;

        if($this->data->scope == "website") return (bool) $this->website_model->whereId($value)->first() && isset($website_id) ? $website_id == $value : true;

        if($this->data->scope == "channel")  return (bool) Channel::whereId($value)->first() && isset($website_id) ? in_array($value, $this->website_model->find($website_id)->channels->pluck('id')->toArray()) : true;

        if($this->data->scope == "store")  return (bool) Store::whereId($value)->first() && isset($website_id) ? in_array($value, $this->website_model->find($website_id)->channels->mapWithKeys(function($channel){
            return $channel->stores->pluck('id');
        })->toArray()) : true;
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Scope Id';
    }
}
