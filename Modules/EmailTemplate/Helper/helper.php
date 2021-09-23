<?php

use \Modules\EmailTemplate\Entities\EmailTemplate;

if (!function_exists('hc_include_email_template')) {
    function hc_include_email_template($path, $scope, $scope_id)
    {
        $template = EmailTemplate::findOrFail(1);
        return $template->content;
    }
}

