<?php

use \Modules\EmailTemplate\Entities\EmailTemplate;

function hc_include_email_template($path, $scope, $scope_id)
{
    $template = EmailTemplate::findOrFail(1);
    return $template->content;
}

