<?php
namespace Modules\Page\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class PageScopeFactory extends Factory
{
    protected $model = \Modules\Page\Entities\PageScope::class;

    public function definition(): array
    {
        $data["scope"] = Arr::random([ "website", "store" ]);

        switch ($data["scope"])
        {
            case "website":
                $data["scope_id"] = Website::factory()->create()->id;
                break;
    
            case "store":
                $data["scope_id"] = Store::factory()->create()->id;
                break;
        }
        
        return $data;
    }
}

