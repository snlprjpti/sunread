<?php

namespace Modules\NavigationMenu\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\NavigationMenu\Entities\NavigationMenuItem;
use Modules\Core\Entities\Website;

class NavigationMenuItemScopeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        $location_fields = config("navigation_menu.location");

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return 'Scope Id does not belong to this NavigationMenuItem';
    }
}
