<?php

namespace Modules\Tax\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class TaxRate extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "identifier" ];
    protected $fillable = [ "country_id", "region_id", "identifier", "use_zip_range", "zip_code", "postal_code_from", "postal_code_to", "tax_rate" ];
}
