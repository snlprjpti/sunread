<?php

namespace Modules\Coupon\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Coupon\Entities\Coupon;

class AllowCouponResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "coupon_id" => new Coupon($this->whenLoaded("coupon_id")),
            "model_type" => $this->model_type,
            "model_id" => $this->model_id,
            "status" => $this->status,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
