<?php

namespace Modules\Core\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class BaseUnitTestCase extends TestCase
{
    use WithoutMiddleware, WithoutEvents;
    
    public array $headers;
    public array $request_data;

    public function setUp(): void
    {
        parent::setUp();
        $this->headers = $this->createHeader();

        $this->hasWebsiteHost = true;
        $this->hasChannel = true;
        $this->hasStore = true;
    }

    public function createHeader(): void
    {
        $this->website = Website::factory()->create();

        if ($this->hasWebsiteHost) $this->headers["hc-host"] = $this->website->hostname;
        if ($this->hasChannel) {
            $this->channel = $this->createChannel();
            $this->headers["hc-channel"] = $this->channel->code;
        } 
        if ($this->hasStore) {
            $this->channel = $this->channel ?? $this->createChannel();
            $this->store = $this->createStore();
            $this->headers["hc-store"] = $this->store->code;
        }
    }

    public function createChannel(): object
    {
        $channelData = array_merge(Channel::factory()->make()->toArray(), [
            "website_id" => $this->website->id
        ]);
        return Channel::create($channelData); 
    }

    public function createStore(): object
    {
        $storeData = array_merge(Store::factory()->make()->toArray(), [
            "channel_id" => $this->channel->id
        ]);
        return Store::create($storeData);
    }

    public function createRequest(): object
    {
        dd($this->headers);  
    }


}
