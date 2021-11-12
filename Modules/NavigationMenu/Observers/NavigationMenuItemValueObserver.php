<?php


namespace Modules\NavigationMenu\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\NavigationMenu\Entities\NavigationMenuItemValue;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class NavigationMenuItemValueObserver
{
    public function created(NavigationMenuItemValue $navigation_menu_item_value)
    {

    }

    public function updated(NavigationMenuItemValue $navigation_menu_item_value)
    {

    }

    public function deleted(NavigationMenuItemValue $navigation_menu_item_value)
    {
    }
}
