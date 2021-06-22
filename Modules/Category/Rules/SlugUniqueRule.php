<?php

namespace Modules\Category\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Category\Entities\Category;

class SlugUniqueRule implements Rule
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
        $category_exist = $this->model->whereParentId($this->data->parent_id)->whereWebsiteId($this->data->website_id)->whereSlug($value)->first();
        
        return ($category_exist) ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Slug has already taken';
    }
}
