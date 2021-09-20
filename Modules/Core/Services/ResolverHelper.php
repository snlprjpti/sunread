<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Request;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Exceptions\PageNotFoundException;
use Modules\Core\Facades\CoreCache;
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

            $fallback_id = config("website.fallback_id");
            if ($request->hasHeader('hc-host')) $website = CoreCache::getWebsite($request->header("hc-host"));
            else $website = Website::whereId($fallback_id)->firstOrFail();

            $websiteData = collect($website)->only(["id","name","code", "hostname"])->toArray();
            $channel = $this->getChannel($request, $website);

            $websiteData["channel"] = collect($channel)->only(["id","name","code"])->toArray();
            $websiteData["channel"]["store"] = Store::find($channel->default_store_id)?->only(["id","name","code"]);
            
            if ( $channel->default_store_id && $request->header("hc-store") ) {
                $store = $this->getStore($request, $website, $channel);
                $websiteData["channel"]["store"] = collect($store)->only(["id","name","code"])->toArray();
                $language = SiteConfig::fetch("store_locale", "store", $store->id);
                $websiteData["channel"]["store"]["locale"] = $language?->code;
            }
            elseif (!$channel->default_store_id && !$request->header("hc-store")) {
                $store = json_decode(CoreCache::getChannelAllStore($website, $channel)[0]);
                $websiteData["channel"]["store"] = [
                    "id" => $store->id,
                    "name" => $store->name,
                    "code" => $store->code
                ];
                $language = SiteConfig::fetch("store_locale", "store", $store->id);
                $websiteData["channel"]["store"]["locale"] = $language?->code;
            }
            elseif (!$channel->default_store_id && $request->header("hc-store")) {
                $store = $this->getStore($request, $website, $channel);
                $websiteData["channel"]["store"] = collect($store)->only(["id","name","code"])->toArray();
                $language = SiteConfig::fetch("store_locale", "store", $store->id);
                $websiteData["channel"]["store"]["locale"] = $language?->code;
            }

            $store_data = collect(CoreCache::getChannelAllStore($website, $channel))->map(function ($store) {
                $data = json_decode($store);
                return [
                    "id" => $data->id,
                    "name" => $data->name,
                    "code" => $data->code,
                ];
            });
            $websiteData["stores"] = $store_data;

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
            if($channel_code) { 
                $channel = CoreCache::getChannel($website, $channel_code);
            }
            else {
                $channel = $this->checkCondition("website_default_channel", $website);  
                if(!$channel) {
                    $cache = CoreCache::getWebsiteAllChannel($website);
                    if(!$cache) throw new PageNotFoundException(__("core::app.response.not-found", ["name" => "Channel"]));
                    $channel = json_decode($cache[0]);
                }
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
            $channel = $this->getChannel($request, $website);

            if($store_code) {
                $store = CoreCache::getStore($website, $channel, $store_code);
                $channel_store_ids = Store::whereChannelId($channel->id)->get()->pluck("id")->toArray();
                if (!in_array($store->id, $channel_store_ids)) throw new PageNotFoundException(__("core::app.response.not-found", ["name" => "Store"]));
            }
            else {
                $store = $this->checkCondition("website_default_store", $website);
                if(!$store) {
                    $cache = CoreCache::getChannelAllStore($website, $channel);
                    if(!$cache) throw new PageNotFoundException(__("core::app.response.not-found", ["name" => "Store"]));
                    $store = json_decode($cache[0]);
                }
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