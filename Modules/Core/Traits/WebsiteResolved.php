<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Request;
use Modules\Core\Entities\Website;

trait WebsiteResolved
{
    public function getDomain(): string
    {
        $naked_domain = str_replace(["http://", "https://", "/"], "", Request::root());
        $root_domain = explode(":", $naked_domain)[0];

        return $root_domain;
    }

    public function resolveWebsite(): ?object
    {
        $domain = $this->getDomain();
        return Website::whereHostname($domain)->first();
    }
}
