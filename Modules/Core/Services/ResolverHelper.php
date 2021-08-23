<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\SiteConfig;
use Modules\Page\Entities\Page;

class ResolverHelper {

    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    public function getDomain(?string $naked_domain = null): string
    {
        $naked_domain = str_replace(["http://", "https://"], "", $naked_domain ?? Request::root());
        $naked_domain = explode("/", $naked_domain)[0];
        $root_domain = explode(":", $naked_domain)[0];

        return $root_domain;
    }

    public function fetch(object $request, ?callable $callback = null): array
    {
        try
        {
            $websiteData = [];
            $domain = $this->getDomain();

            $fallback_id = config("website.fallback_id");
            if ($request->hasHeader('hc-host')) {
                $fallback_id = Website::whereHostname($request->header("hc-host"))->firstOrFail()?->id;
            }

            $website = Website::whereHostname($domain);
            if ( !$website->exists() && config("website.environment") == "local" ) {
                $website = Website::whereId($fallback_id);
            }
            $website = $website->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            $websiteData = $website->toArray();
            $websiteData["channel"] = $this->getChannel($request, $website);
            $websiteData["store"] = $this->getStore($request, $website, $websiteData["channel"]);
            $websiteData["pages"] = $this->getPages($website);

            if ($callback) $website = $callback($websiteData);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $websiteData;
    }

    public function getChannel(object $request, object $website): ?array
    {
        try
        {
            $channel_code = $request->header("hc-channel");

            if($channel_code) $channel = Channel::whereCode($channel_code)->whereWebsiteId($website->id)->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            else $channel = ($channel = $this->checkCondition("website_default_channel", $website)) ? $channel->firstOrFail() : null;
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $channel ? $channel->toArray() : $channel;
    }

    public function getStore(object $request, object $website, ?array $channel): ?array
    {
        try
        {
            $store_code = $request->header("hc-store");

            if($store_code && $channel) $store = Store::whereCode($store_code)->whereChannelId($channel["id"])->select(["id","name","code"])->setEagerLoads([])->firstOrFail();
            else $store = ($store = $this->checkCondition("website_default_store", $website)) ? $store->firstOrFail() : null;
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $store ? $store->toArray() : $store;
    }

    public function getPages(object $website): ?array
    {
        try
        {
           $pages = Page::whereWebsiteId($website->id)->get();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $pages ? $pages->toArray() : $pages;
    }

    public function checkCondition(string $slug, object $website): ?object
    {
        return SiteConfig::fetch($slug, "website", $website->id);
    }
}