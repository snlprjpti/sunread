<?php

namespace Modules\Sales\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Country\Entities\City;
use Modules\Country\Entities\Country;
use Modules\Country\Entities\Region;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = ["order_id", "customer_id", "customer_address_id", "address_type", "first_name", "middle_name", "last_name", "phone", "email", "address_line_1", "address_line_2", "postal_code", "country_id", "region_id", "city_id", "region_name", "city_name", "vat_number"];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
