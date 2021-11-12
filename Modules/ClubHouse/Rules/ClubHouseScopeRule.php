<?php

namespace Modules\ClubHouse\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\ClubHouse\Entities\ClubHouse;
use Modules\Core\Entities\Website;

class ClubHouseScopeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $data, $website_model;

    public function __construct($data, $id = null)
    {
        $this->data = $data;
        $this->id = $id;
        $this->website_model = new Website();
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        if($this->id) $website_id = ClubHouse::findOrFail($this->id)->website_id ;

        if(!isset($website_id) && $this->data->website_id) $website_id = $this->data->website_id;

        if($this->data->scope == "website" && isset($website_id)) return (bool) $website_id == $value;

        if($this->data->scope == "channel" && isset($website_id))  return (bool) in_array($value, $this->website_model->find($website_id)->channels->pluck('id')->toArray());

        if($this->data->scope == "store" && isset($website_id))  return (bool) in_array($value, $this->website_model->find($website_id)->channels->map(function ($channel) {
            return $channel->stores->pluck('id');
        })->flatten(1)->toArray());

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return 'Scope Id does not belong to this Clubhouse';
    }
}
