<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

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
}
