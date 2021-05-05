<?php

namespace Modules\UrlRewrite\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UrlRewriteResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
