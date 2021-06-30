<?php

namespace Modules\Country\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [ "alpha_2_code", "alpha_3_code", "numeric_code", "iso_2_code", "iso_3_code", "dialing_code", "name" ];
}
