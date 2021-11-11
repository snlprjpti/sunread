<?php

namespace Modules\Sales\Traits;

use Exception;
use Modules\Sales\Entities\Order;
use Modules\Core\Facades\SiteConfig;

trait HasOrder
{
	public function store(object $request): ?object
	{
		try
		{
			$coreCache = $this->getCoreCache($request);
            $currency_code = SiteConfig::fetch('channel_currency', 'channel', $coreCache?->channel->id);
            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "customer_id" => auth("customer")->id(),
                "store_name" => $coreCache?->store->name,
                "is_guest" => auth("customer")->id() ? 0 : 1,
                "billing_address_id" => $request->billing_address_id,
                "shipping_address_id" => $request->shipping_address_id,
                "currency_code" => $currency_code->code,
                "shipping_method" => $request->shipping_method ?? 'online-delivery',
                "shipping_method_label" => $request->shipping_method_label ?? 'online-delivery',
                "payment_method" => $request->payment_method ?? 'stripe',
                "payment_method_label" => $request->payment_method_label ?? 'stripe',
                "sub_total" => $request->sub_total ?? 0.00,
                "sub_total_tax_amount" => $request->sub_total_tax_amount ?? 0.00,
                "tax_amount" => $request->tax_amount ?? 0.00,
                "grand_total" => $request->grand_total ?? 0.00,
                "total_items_ordered" => $request->total_items_ordered ?? 0.00,
                "total_qty_ordered" => $request->total_qty_ordered ?? 0.00
            ];

            if ( $data['is_guest'] ) {
                $customer_data = [
                    "customer_email" => $request->customer_details['email'],
                    "customer_first_name" => $request->customer_details['first_name'],
                    "customer_middle_name" => $request->customer_details['middle_name'],
                    "customer_last_name" => $request->customer_details['last_name'],
                    "customer_phone" => $request->customer_details['phone'],
                    "customer_taxvat" => $request->customer_details['taxvat'],
                    "customer_ip_address" => GeoIp::requestIp(),
                    "status" => "pending"
                ];
            }
            $customer_data = isset($data['is_guest']) ? $customer_data : []; 
            $data = array_merge($data, $customer_data);

            $order = Order::create($data);
		}
		catch (Exception $exception)
		{
			throw $exception;
		}

		return $order;
	}
}
