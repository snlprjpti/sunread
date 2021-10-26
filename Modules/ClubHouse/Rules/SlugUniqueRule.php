<?php

namespace Modules\ClubHouse\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\ClubHouse\Traits\HasScope;

class SlugUniqueRule implements Rule
{
    use HasScope;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $data, $clubHouse;

    public function __construct(object $data, ?object $clubHouse = null)
    {
        $this->data = $data;
        $this->clubHouse = $clubHouse;
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
        $arr = $this->data->toArray();
        return (bool) !$this->checkSlug($arr, $value, $this->clubHouse);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Slug has already been taken';
    }
}
