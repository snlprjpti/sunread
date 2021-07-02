<?php

namespace Modules\Tax\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxRateTaxRule extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [ "tax_rate_id", "tax_rule_id" ];
}
