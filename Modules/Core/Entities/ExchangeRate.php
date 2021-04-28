<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [ 'source_currency', 'target_currency', 'rate' ];

    // Get From Currency
    public function source()
    {
        return $this->belongsTo(Currency::class, 'source_currency');
    }

    // Get To Currency
    public function target()
    {
        return $this->belongsTo(Currency::class, 'target_currency');
    }
}
