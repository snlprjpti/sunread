<?php

namespace Modules\Core\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class BaseUnitTestCase extends TestCase
{
    use WithoutMiddleware, WithoutEvents;
    
    
    public function setUp(): void
    {

    }


}
