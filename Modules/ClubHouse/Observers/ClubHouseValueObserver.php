<?php


namespace Modules\ClubHouse\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\ClubHouse\Entities\ClubHouseValue;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Traits\ElasticSearch\PrepareIndex;

class ClubHouseValueObserver
{
    use PrepareIndex;

    public function created(ClubHouseValue $clubhouse_value)
    {

    }

    public function updated(ClubHouseValue $clubhouse_value)
    {
        if($clubhouse_value->attribute == "title" || $clubhouse_value->attribute == "slug")
        $this->preparingIndexData($clubhouse_value->clubHouse);
    }

    public function deleted(ClubHouseValue $category_value)
    {
    }

    public function deleting(ClubHouseValue $clubhouse_value)
    {
        if($clubhouse_value->attribute == "title" || $clubhouse_value->attribute == "slug")
        $this->preparingIndexData($clubhouse_value->clubHouse);
    }
}
