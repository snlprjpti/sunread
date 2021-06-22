<?php

namespace Modules\Category\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Category\Entities\Category;

class WebsiteRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $data; 

    public function __construct($data)
    {
        $this->data = $data;
        $this->model = new Category();
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
        return (bool) !strcmp(strval($this->model->find($this->data->parent_id)->website_id), $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Parent Category does not belong to this website.';
    }
}
