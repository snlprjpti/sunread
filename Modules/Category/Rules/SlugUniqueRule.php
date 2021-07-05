<?php

namespace Modules\Category\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Category\Traits\HasScope;

class SlugUniqueRule implements Rule
{
    use HasScope;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $data, $id; 

    public function __construct($data, $id=null)
    {
        $this->data = $data;
        $this->id = $id;
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
        $category_exist = $this->checkSlug($this->data, $value);
        return ($category_exist) ? ($this->id == $category_exist->id ? true : false) : true;
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
