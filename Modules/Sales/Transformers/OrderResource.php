<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "order_items" => OrderItemResource::collection($this->whenLoaded("order_items")),
            "website_id" => $this->website_id,
            "store_id" => $this->store_id,
            "customer_id" => $this->customer_id,
            "store_name" => $this->store_name,
            "is_guest" => (bool) $this->is_guest,
            "billing_address_id" => $this->billing_address_id,
            "shipping_address_id" => $this->shipping_address_id,
            "shipping_method" => $this->shipping_method,
            "shipping_method_label" => $this->shipping_method_label,
            "payment_method" => $this->payment_method,
            "payment_method_label" => $this->payment_method_label,
            "currency_code" => $this->currency_code,
            "discount_amount" => $this->discount_amount,
            "discount_amount_tax" => $this->discount_amount_tax,
            "shipping_amount" => $this->shipping_amount,
            "shipping_amount_tax" => $this->shipping_amount_tax,
            "sub_total" => $this->sub_total,
            "sub_total_tax_amount" => $this->sub_total_tax_amount,
            "tax_amount" => $this->tax_amount,
            "grand_total" => $this->grand_total,
            "weight" => $this->weight,
            "total_items_ordered" => $this->total_items_ordered,
            "total_qty_ordered" => $this->total_qty_ordered,
            "customer_email" => $this->customer_email,
            "customer_first_name" => $this->customer_first_name,
            "customer_middle_name" => $this->customer_middle_name,
            "customer_last_name" => $this->customer_last_name,
            "customer_phone" => $this->customer_phone,
            "customer_taxvat" => $this->customer_taxvat,
            "customer_ip_address" => $this->customer_ip_address,
            "status" => $this->status,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
