<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Illuminate\Support\Facades\Request;
use Modules\Core\Facades\SiteConfig;
use Modules\Page\Entities\Page;

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
            $host = $request->header('hc-host');
            $website = Cache::rememberForever("website_{$host}", function() use($host){
                $fallback_id = config("website.fallback_id");
                return ($host) ?  Website::whereHostname($host)->firstOrFail() : Website::whereId($fallback_id);
            });

            $website->channel = $this->getChannel($request, $website);
            $website->store = $this->getStore($request, $website);
            $website->pages = $this->getPages($website);

            if ($callback) $website = $callback($website);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $website;
    }

    public function getChannel(object $request, object $website): object
    {
        try
        {
            $channel_code = $request->header("hc-channel");
            $channel = Cache::rememberForever("channel_{$channel_code}", function() use($channel_code, $website) {
                return ($channel_code) ? Channel::whereCode($channel_code)->firstOrFail() : $this->checkConditionChannel($website);
            });
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $channel;
    }

    public function getStore(object $request, object $website): object
    {
        try
        {
            $store_code = $request->header("hc-store");
            $store = Cache::rememberForever("store_{$store_code}", function() use($store_code, $website) {
                return ($store_code) ? Store::whereCode($store_code)->firstOrFail() : $this->checkConditionStore($website);
            });
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $store;
    }

    public function getPages(object $website): object
    {
        try
        {
           $pages = Page::whereWebsiteId($website->id)->get();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $pages;
    }

    public function checkConditionChannel(object $website): object
    {
        return SiteConfig::fetch("website_default_channel", "website", $website->id);
    }

    public function checkConditionStore(object $website): object
    {
        return SiteConfig::fetch("website_default_store", "website", $website->id);
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
