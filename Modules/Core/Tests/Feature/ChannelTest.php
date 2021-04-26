<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\Channel;
use Modules\Core\Tests\BaseTestCase;

class ChannelTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
    }

    public function testAdminCanFetchChannels()
    {
        $channel = Channel::factory()->create();
        $response = $this->get(route('admin.channels.index'));

        $response->assertJsonFragment([
            "code" => $channel->code,
            "name" => $channel->name
        ]);
    }
}
