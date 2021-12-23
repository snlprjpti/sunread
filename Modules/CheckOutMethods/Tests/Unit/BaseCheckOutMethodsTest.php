<?php

namespace Modules\CheckOutMethods\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use Modules\CheckOutMethods\Services\BaseCheckOutMethods;
use Modules\Core\Tests\BaseUnitTestCase;

class BaseCheckOutMethodsTest extends BaseUnitTestCase
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

    // public function test_class_has_all_method(): void
    // {
    //     $method_list = $this->base_checkout_method_class->all();
    //     dd($method_list);
    //     $this->assertArrayHasKey("delivery_methods", $method_list);
    //     $this->assertArrayHasKey("payment_methods", $method_list);
    //     foreach ($method_list as $method) {
    //         dd($method);
            
    //     }
    // }

    public function test_class_get_method_should_return_valid_data(): void
    {
        $checkout_method = Arr::random(["payment_methods", "delivery_methods"]);
        $data = $this->base_checkout_method_class->get($checkout_method);
        $this->assertIsObject($data);
        foreach ( ["title", "slug", "check_out_method", "repository"] as $attribute) $this->assertArrayHasKey($attribute, $data->first());
    }

    public function test_class_fetch_method_should_return_valid_data(): void
    {
        $method = Arr::random(["bank_transfer", "flat_rate", "free_shipping"]);
        $data = $this->base_checkout_method_class->fetch($method);
        $this->assertIsObject($data);
        foreach (["title", "slug", "check_out_method", "repository"] as $attribute) $this->assertArrayHasKey($attribute, $data->toArray());
    }

    public function test_class_process_method_should_return_valid_data(): void
    {
        $request = Request::create("/", "GET", ["shipping_methods" => "flat_rate"]);
        dd($this->createRequest());
        $request->headers->add(["hc-host" => "", ]);
        dd($request->header("hc-host"));

        // $valid = "bank_transfer";
        // $request = $this->mock(Request::class, static function (MockInterface $mock) use ($valid): void {
        //     $mock->shouldReceive('validated')->once()->andReturn($valid);
        // });
        $this->assertInstanceOf(MockInterface::class, resolve(Request::class));
        dd("asd");

        // $request->payment_method = Arr::random(["klarna", "bank_transfer", "cash_on_delivery"]);
        dd($request);
        
        // $data = $this->base_checkout_method_class->process(, );
    }


}
