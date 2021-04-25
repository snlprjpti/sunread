<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Tests\BaseTest;

class ChannelTest extends BaseTest
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
    }
}
