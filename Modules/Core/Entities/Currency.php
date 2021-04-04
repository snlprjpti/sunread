<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\CurrencyExchangeRate;

class Currency extends Model
{
    protected $fillable = [ 'code', 'name', 'symbol' ];

    // Set Currency Code in Capitalized Letters
    public function setCodeAttribute($code)
    {
        $this->attributes['code'] = strtoupper($code);
    }
    
    // Get Exchange rate from associated Currency
    public function CurrencyExchangeRate()
    {
        return $this->hasOne(CurrencyExchangeRate::class, 'target_currency');
    }
}
