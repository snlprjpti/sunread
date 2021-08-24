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
                $website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
            }
            else {
                $website = Website::whereId($fallback_id)->firstOrFail();
            }
            $websiteData = $website->only(["id","name","code", "hostname"]);

            $channel = $this->getChannel($request, $website);
            $websiteData["channel"] = $channel->only(["id","name","code"]);

            $store = $this->getStore($request, $website, $channel);
            $websiteData["store"] = $store->only(["id","name","code"]);

            $websiteData["pages"] = $this->getPages($website);

            if ($callback) $website = $callback($websiteData);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $websiteData;
    }

    public function getChannel(object $request, object $website): object
    {
        try
        {
            $channel_code = $request->header("hc-channel");

            if($channel_code) $channel = Channel::whereCode($channel_code)->whereWebsiteId($website->id)->firstOrFail();
            else {
                $channel= $this->checkCondition("website_default_channel", $website)?->firstOrFail(); 
                if(!$channel) $channel = $website->channels()->firstOrFail();
            }    
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $channel;
    }

    public function getStore(object $request, object $website, object $channel): object
    {
        try
        {
            $store_code = $request->header("hc-store");

            if($store_code) $store = Store::whereChannelId($channel->id)->whereCode($store_code)->firstOrFail();
            else {
                $store = $this->checkCondition("website_default_store", $website)?->firstOrFail();
                if(!$store) $store = $channel->stores()->firstOrFail();
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $store;
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