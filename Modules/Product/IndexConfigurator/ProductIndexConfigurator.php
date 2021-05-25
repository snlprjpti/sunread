<?php

namespace Modules\Product\IndexConfigurator;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class ProductIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'sail_Racing';
    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}