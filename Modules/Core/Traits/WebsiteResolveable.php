<?php

namespace Modules\Core\Traits;

use Exception;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Illuminate\Support\Facades\Request;

trait WebsiteResolveable
{
    public function getDomain(?string $naked_domain = null): string
    {
        $naked_domain = str_replace(["http://", "https://"], "", $naked_domain ?? Request::root());
        $naked_domain = explode("/", $naked_domain)[0];
        $root_domain = explode(":", $naked_domain)[0];

        return $root_domain;
    }

    public function resolveWebsite(?string $website_domain = null, ?callable $callback = null): ?object
    {
        try
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

            $resolved = $website->with("channels.stores")->firstOrFail();
            if ($callback) $resolved = $callback($resolved);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $resolved;
    }


    public function resolveWebsiteUpdate(object $request, ?callable $callback = null): ?object
    {
        try
        {
            $domain = $this->getDomain();

            $fallback_id = config("website.fallback_id");
            if ($request->hasHeader('host')) {
                $fallback_id = Website::whereHostname($request->header("host"))->firstOrFail()?->id;
            }
            $website = Website::whereHostname($domain)->first();
            if ( !$website->exists() && config("website.environment") == "local" ) {
                $website = Website::whereId($fallback_id)->firstOrFail();
            }

            if($request->hasHeader("channel")){
                $channel_code = $request->header("channel");
                $channel = Store::whereCode($channel_code)->firstOrFail();
            }
            else {
                $channel = ($channel = $this->checkCondition()->first()) ? Channel::whereId($channel->value)->firstOrFail() : null;
            }

            if ($callback) $website = $callback(array_combine($website->toArray(), $channel->toArray()));
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $website;
    }


    public function checkCondition(): object
    {
        return \Modules\Core\Entities\Configuration::where([
            ['path', "website_default_channel"]
        ]);
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
