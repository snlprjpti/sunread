<?php

namespace Modules\CheckOutMethods\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\CheckOutMethods\Services\BaseCheckOutMethods;

class BaseCheckOutMethodsTest extends TestCase
{
    protected object $base_checkout_method_class;
    protected string $class;
    protected array $class_attributes;
    protected array $methods;

    public function setUp(): void
    {
        parent::setUp();
        $this->class = BaseCheckOutMethods::class;
        $this->base_checkout_method_class = new $this->class;
        $this->class_attributes = [
            "checkout_methods",
            "method_attributes",
            "check_out_method",
            "get_initial_repository",
        ];
        $this->methods = ["delivery_methods", "payment_methods"];
    }

    public function test_has_class_attributes(): void
    {
        foreach ($this->class_attributes as $attribute) $this->assertClassHasAttribute($attribute, $this->class);
    }

    public function test_class_has_all_method(): void
    {
        $method_list = $this->base_checkout_method_class->all();
        dd($method_list);
        $this->assertArrayHasKey("delivery_methods", $method_list);
        $this->assertArrayHasKey("payment_methods", $method_list);
        foreach ($method_list as $method) {
            dd($method);
            
        }
    }

    public function test_class_get_method_has_keys(): void
    {
        $method = 
    }
}
