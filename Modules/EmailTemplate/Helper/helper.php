<?php

use Modules\Core\Facades\SiteConfig;

if (!function_exists('hc_include_email_template'))
{
    function hc_include_email_template($path)
    {
        $template = SiteConfig::fetch($path, "store", config('store'));
        return $template->content;
    }
}

