<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class CoreCacheHelper
{
    protected $website, $channel, $store, $old_website_hostname, $old_channel_code;

    public function __construct(Website $website, Channel $channel, Store $store)
    {
        $this->website = $website;
        $this->channel = $channel;
        $this->store = $store;
    }

    public function createWebsiteCache(object $website)
    {
        unset($website->channels, $website->stores);

        Redis::set("sf_website_{$website->hostname}", $website);
    }

    public function updateBeforeWebsiteCache(int $website_id)
    {
        $website = Website::findOrFail($website_id);
        $this->old_website_hostname = $website->hostname;
        Redis::del("sf_website_{$website->hostname}");
    }

    public function updateWebsiteCache(object $website)
    {
        unset($website->channels, $website->stores);
        if( $this->old_website_hostname != $website->hostname ) {
            if( count(Redis::keys("sf_c_website_{$this->old_website_hostname}_*")) > 0 ) Redis::del(Redis::keys("sf_c_website_{$this->old_website_hostname}_*"));
            if( count(Redis::keys("sf_s_website_{$this->old_website_hostname}_*")) > 0) Redis::del(Redis::keys("sf_s_website_{$this->old_website_hostname}_*"));
        }

        Redis::set("sf_website_{$website->hostname}", $website);
    }

    public function deleteBeforeWebsiteCache(int $website_id)
    {
        $channels = $this->channel->whereWebsiteId($website_id)->get();
        foreach ($channels as $channel)
        {
            Redis::del("sf_channel_{$channel->code}");
        }
        $stores = $this->website->find($website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });
        foreach ($stores as $store)
        {
            Redis::del("sf_store_{$store->code}");
        }
    }

    public function deleteWebsiteCache(object $website)
    {
        Redis::del("sf_website_{$website->hostname}");
        if( count(Redis::keys("sf_c_website_{$website->hostname}_*")) > 0 ) Redis::del(Redis::keys("sf_c_website_{$website->hostname}_*"));
        if( count(Redis::keys("sf_s_website_{$website->hostname}_*")) > 0) Redis::del(Redis::keys("sf_s_website_{$website->hostname}_*"));
    }

    public function createChannelCache(object $channel)
    {
        $website_hostname = $channel->website->hostname;

        unset($channel->website, $channel->stores);
        Redis::set("sf_c_website_{$website_hostname}_channel_{$channel->code}", $channel);
        Redis::set("sf_channel_{$channel->code}", $channel);
    }

    public function updateBeforeChannelCache(int $channel)
    {
        $channel = Channel::findOrFail($channel);
        $this->old_channel_code = $channel->code;

        Redis::del("sf_c_website_{$channel->website->hostname}_channel_{$channel->code}");
        Redis::del("sf_channel_{$channel->code}");
    }

    public function updateChannelCache(object $channel)
    {
        $website_hostname = $channel->website->hostname;

        if( $this->old_channel_code != $channel->code ) {
            if( count(Redis::keys("sf_s_website_{$channel->website->hostname}_channel_{$this->old_channel_code}_store_*")) > 0 ) Redis::del(Redis::keys("sf_s_website_{$channel->website->hostname}_channel_{$this->old_channel_code}_store_*"));
        }
        unset($channel->website, $channel->stores);
        Redis::set("sf_c_website_{$website_hostname}_channel_{$channel->code}", $channel);
        Redis::set("sf_channel_{$channel->code}", $channel);
    }

    public function deleteBeforeChannelCache(int $channel_id)
    {
        $stores = $this->store->whereChannelId($channel_id)->get();
        foreach ($stores as $store)
        {
            Redis::del("sf_store_{$store->code}");
        }
    }

    public function deleteChannelCache(object $channel)
    {
        Redis::del("sf_c_website_{$channel->website->hostname}_channel_{$channel->code}");
        if( count(Redis::keys("sf_s_website_{$channel->website->hostname}_channel_{$channel->code}_*")) > 0 ) Redis::del(Redis::keys("sf_s_website_{$channel->website->hostname}_channel_{$channel->code}_*"));
        Redis::del("sf_channel_{$channel->code}");
    }

    public function createStoreCache(object $store)
    {
        $channel = $store->channel;
        $store["website_id"] = $channel->website->id;

        unset($store->channel, $store->website);
        Redis::set("sf_s_website_{$channel->website->hostname}_channel_{$channel->code}_store_{$store->code}", $store);
        Redis::set("sf_store_{$store->code}", $store);
    }

    public function updateBeforeStoreCache(int $store)
    {
        $store = Store::findOrFail($store);
        Redis::del("sf_s_website_{$store->channel->website->hostname}_channel_{$store->channel->code}_store_{$store->code}");
        Redis::del("sf_store_{$store->code}");
    }

    public function deleteStoreCache(object $store)
    {
        Redis::del("sf_s_website_{$store->channel->website->hostname}_channel_{$store->channel->code}_store_{$store->code}");
        Redis::del("sf_store_{$store->code}");
    }

    public function getWebsite(string $website_hostname): ?object
    {
        $website = json_decode(Redis::get("sf_website_{$website_hostname}"));
        if( ! $website ) {
            $website = $this->website->whereHostname($website_hostname)->setEagerLoads([])->firstOrFail();
            Redis::set("sf_website_{$website_hostname}", $website);
        }

        return $website;
    }

    public function getChannel(object $website, string $channel_code): ?object
    {
        $channel = json_decode(Redis::get("sf_c_website_{$website->hostname}_channel_{$channel_code}"));
        if( !$channel ) {
            $channel = $this->channel->whereWebsiteId($website->id)->whereCode($channel_code)->setEagerLoads([])->firstOrFail();
            unset($channel->stores, $channel->website);

            Redis::set("sf_c_website_{$website->hostname}_channel_{$channel_code}", $channel);
            Redis::SETNX("sf_channel_{$channel_code}", $channel);
        }

        return $channel;
    }

    public function getStore(object $website, object $channel, string $store_code): ?object
    {
        $store = json_decode(Redis::get("sf_s_website_{$website->hostname}_channel_{$channel->code}_store_{$store_code}"));
        if( !$store ) {
            $store = $this->store->whereCode($store_code)->setEagerLoads([])->firstOrFail();
            $store["website_id"] = $website->id;
            unset($store->channel, $store->website);

            Redis::set("sf_s_website_{$website->hostname}_channel_{$channel->code}_store_{$store_code}", $store);
            Redis::SETNX("sf_store_{$store_code}", $store);
        }

        return $store;
    }

    public function getWebsiteAllChannel(object $website): ?array
    {
        $keys = Redis::keys("sf_c_website_{$website->hostname}_*");
        if(!$keys) {
            $channels = $this->channel->whereWebsiteId($website->id)->setEagerLoads([])->get();
            foreach($channels as $channel) {
                unset($channel->stores, $channel->website);

                Redis::SETNX("sf_c_website_{$website->hostname}_channel_{$channel->code}", $channel);
                Redis::SETNX("sf_channel_{$channel->code}", $channel);
            }
            $keys = Redis::keys("sf_c_website_{$website->hostname}_*");
        }

        return $keys ? Redis::mget($keys) : null;
    }

    public function getWebsiteAllStore(object $website): ?array
    {
        $keys = Redis::keys("sf_s_website_{$website->hostname}_*");
        if(!$keys) {
            $stores = $this->website->find($website->id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });
            foreach($stores as $store) {
                $channel = $this->channel->findOrFail($store->channel_id);
                $store["website_id"] = $website->id;

                unset($store->channel, $store->website);
                Redis::SETNX("sf_s_website_{$website->hostname}_channel_{$channel->code}_store_{$store->code}", $store);
                Redis::SETNX("sf_store_{$store->code}", $store);
            }
            $keys = Redis::keys("sf_s_website_{$website->hostname}_*");
        }

        return $keys ? Redis::mget($keys) : null;
    }

    public function getChannelAllStore(object $website, object $channel): ?array
    {
        $keys = Redis::keys("sf_s_website_{$website->hostname}_channel_{$channel->code}_*");

        if(!$keys) {
            $stores = $this->store->whereChannelId($channel->id)->setEagerLoads([])->get();
            foreach($stores as $store) {
                $store["website_id"] = $website->id;

                unset($store->channel, $store->website);
                Redis::SETNX("sf_s_website_{$website->hostname}_channel_{$channel->code}_store_{$store->code}", $store);
                Redis::SETNX("sf_store_{$store->code}", $store);
            }
            $keys = Redis::keys("sf_s_website_{$website->hostname}_channel_{$channel->code}_*");
        }

        return $keys ? Redis::mget($keys) : null;
    }

    public function getChannelWithoutWebsite(string $channel_code): ?object
    {
        $channel = json_decode(Redis::get("sf_channel_{$channel_code}"));

        if( !$channel ) {
            $channel = $this->channel->whereCode($channel_code)->setEagerLoads([])->firstOrFail();
            $website = $channel->website;
            unset($channel->stores, $channel->website);

            Redis::set("sf_c_website_{$website->hostname}_channel_{$channel_code}", $channel);
            Redis::SETNX("sf_channel_{$channel_code}", $channel);
        }

        return $channel;
    }

    public function getStoreWithoutWebsite(string $store_code): ?object
    {
        $store = json_decode(Redis::get("sf_store_{$store_code}"));

        if( !$store ) {
            $store = $this->store->whereCode($store_code)->setEagerLoads([])->firstOrFail();
            $channel = $store->channel;
            $website = $channel->website;
            unset($store->channel, $store->website);
            Redis::set("sf_c_website_{$website->hostname}_channel_{$channel->code}_store_{$store_code}", $store);
            Redis::SETNX("sf_store_{$store_code}", $store);
        }

        return $store;
    }
}
