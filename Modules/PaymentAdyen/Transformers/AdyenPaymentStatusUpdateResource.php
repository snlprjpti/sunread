<?php

namespace Modules\PaymentAdyen\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AdyenPaymentStatusUpdateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
           "message" => $this['message'],
           "resultCode" => $this['resultCode']
        ];
    }
}
