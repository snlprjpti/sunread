<?php

namespace Modules\Core\Traits;

use Exception;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Configuration;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Illuminate\Support\Facades\Request;
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

    public function resolveWebsiteUpdate(object $request, ?callable $callback = null): ?array
    {
        try
        {
            $domain = $this->getDomain();

            $fallback_id = config("website.fallback_id");
            if ($request->hasHeader('hc-host')) {
                $fallback_id = Website::whereHostname($request->header("hc-host"))->firstOrFail()?->id;
            }

            $collection["website"] = Website::whereHostname($domain)->select(["id","name","code"])->setEagerLoads([])->first();
            if ( !$collection["website"] && config("website.environment") == "local" ) {
                $collection["website"] = Website::whereId($fallback_id)->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            }

            $collection["channel"] = $this->getChannel($request);
            $collection["store"] = $this->getStore($request);
            $collection["pages"] = $this->getPages($collection["website"]);

            if ($callback) $collection = $callback($collection);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $collection;
    }

    public function getChannel(object $request): object
    {
        try
        {
            if($request->hasHeader("hc-channel")){
                $channel_code = $request->header("hc-channel");
                $channel = Channel::whereCode($channel_code)->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            }
            else {
                $channel = ($channel = $this->checkConditionChannel()->first()) ? Channel::whereId($channel->value)->select(["id","name","code"])->setEagerLoads([])->firstOrFail() : null;
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $channel;
    }

    public function getStore(object $request): object
    {
        try
        {
            if($request->hasHeader("hc-store")){
                $store_code = $request->header("hc-store");
                $store = Store::whereCode($store_code)->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            }
            else {
                $store = ($store = $this->checkConditionStore()->first()) ? Store::whereId($store->value)->select(["id","name","code"])->setEagerLoads([])->firstOrFail() : null;
            }
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
           $pages = Page::whereWebsiteId($website->id)->select(["id","slug as code","title"])->get()->keyBy("title");
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $pages;
    }

    public function checkConditionChannel(): object
    {
        return Configuration::where([
            ['path', "website_default_channel"]
        ]);
    }

    public function checkConditionStore(): object
    {
        return Configuration::where([
            ['path', "website_default_store"]
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
