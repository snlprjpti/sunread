<?php

use Modules\Core\Facades\SiteConfig;

/**
 * function to get content of email template from configuration
 */
if (!function_exists('hc_include_email_template'))
{
    function hc_include_email_template($path)
    {
        return  ($template = SiteConfig::fetch($path, "store", config('store'))) ? $template->content : null;
    }
}
