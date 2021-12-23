<?php

namespace Modules\CheckOutMethods\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CheckOutMethods\Services\CheckOutProcessResolver;

class CheckOutProcessResolverTest extends TestCase
{
    protected string $class;
    protected object $checkout_process_resolver;
    protected array $class_attributes;

    public function setUp(): void
    {
        parent::setUp();
        $this->class = CheckOutProcessResolver::class;
        $this->checkout_process_resolver = new CheckOutProcessResolver(request());
        $this->class_attributes = [
            "request",
            "method_data",
            "custom_checkout_handler"
        ];
    }

    public function test_has_class_attributes(): void
    {
        foreach ($this->class_attributes as $attribute) $this->assertClassHasAttribute($attribute, $this->class);
    }

    public function test_class_has_check_method(): void
    {
        // dd(request(["slug","asd"]));
        $resolver = $this->checkout_process_resolver;
        $this->assertIsBool($resolver->check("flat_rate", "delivery_methods"));
    }
}
