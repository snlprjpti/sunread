<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Request;
use Modules\Core\Entities\Website;

trait WebsiteResolveable
{
    public function getDomain(): string
    {
        $naked_domain = str_replace(["http://", "https://"], "", Request::root());
        $naked_domain = explode("/", $naked_domain)[0];
        $root_domain = explode(":", $naked_domain)[0];

        return $root_domain;
    }

    public function resolveWebsite(): ?object
    {
        $domain = $this->getDomain();
        $website = ( config("website.environment") == "local" )
            ? Website::find(config("website.fallback_id"))
            : Website::whereHostname($domain)->first();
        return $website;
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
