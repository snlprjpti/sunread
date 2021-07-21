<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Request;
use Modules\Core\Entities\Website;

trait WebsiteResolveable
{
    public function getDomain(?string $naked_domain = null): string
    {
        $naked_domain = str_replace(["http://", "https://"], "", $naked_domain ?? Request::root());
        $naked_domain = explode("/", $naked_domain)[0];
        $root_domain = explode(":", $naked_domain)[0];

        return $root_domain;
    }

    public function resolveWebsite(?string $website_domain = null): ?object
    {
        $domain = $this->getDomain();

        $fallback_id = config("website.fallback_id");
        if ($website_domain !== null) {
            $fallback_id = Website::whereHostname($this->getDomain($website_domain))->firstOrFail()?->id;
        }

        $website = Website::whereHostname($domain);
        if ( !$website->exists() && config("website.environment") == "local" ) {
            $website = Website::whereId($fallback_id);
        }

        return $website->with("channels.stores")->firstOrFail();
    }

    public function fetchAll(object $request, array $with = [], ?callable $callback = null): object
    {
        try
        {
            $rows = ($callback) ? $callback() : $this->model;

            $fetched = parent::fetchAll($request, $with, function () use ($rows) {
                return $rows->whereWebsiteId($this->resolveWebsite()->id);
            });
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function fetch(int $id, array $with = [], ?callable $callback = null): object
    {
        try
        {
            $rows = ($callback) ? $callback() : $this->model;

            $fetched = parent::fetch($id, $with, function () use ($rows) {
                return $rows->whereWebsiteId($this->resolveWebsite()->id);
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }
}
