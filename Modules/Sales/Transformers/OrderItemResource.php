<?php

namespace Modules\Sales\Transformers;

use Modules\Core\Facades\SiteConfig;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Repositories\BaseRepository;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $coreCache = (new BaseRepository())->getCoreCache($request);
        $currency_code = SiteConfig::fetch('channel_currency', 'channel', $coreCache?->channel->id);
        
        return [
            "id" => $this->id,
            "website_id" => $this->website_id,
            "store_id" => $this->store_id,
            "product_id" => $this->product_id,
            "order_id" => $this->order_id,
            "currency_code" => $currency_code,
            "product_options" => $this->product_options,
            "product_type" => $this->product_type,
            "sku" => $this->sku,
            "name" => $this->name,
            "weight" => $this->weight,
            "qty" => $this->qty,
            "cost" => $this->cost,
            "price" => $this->price,
            "price_incl_tax" => $this->price_incl_tax,
            "coupon_code" => $this->coupon_code,
            "discount_amount" => $this->discount_amount,
            "discount_percent" => $this->discount_percent,
            "discount_amount_tax" => $this->discount_amount_tax,
            "tax_amount" => $this->tax_amount,
            "tax_percent" => $this->tax_percent,
            "row_total" => $this->row_total,
            "row_total_incl_tax" => $this->row_total_incl_tax,
            "row_weight" => $this->row_weight,
            "created_at" => $this->created_at?->format("M d, Y H:i A")
        ];
    }
}
