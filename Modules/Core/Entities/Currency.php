<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\CurrencyExchangeRate;


class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'symbol'
    ];

    /**
     * Set currency code in capital
     *
     * @param $code
     * @return void
     */
    public function setCodeAttribute($code)
    {
        $this->attributes['code'] = strtoupper($code);
    }



    /**
     * Get the currency_exchange associated with the currency.
     */
    public function CurrencyExchangeRate()
    {
        return $this->hasOne(CurrencyExchangeRate::class, 'target_currency');
    }
}
