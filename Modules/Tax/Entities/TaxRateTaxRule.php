<?php

namespace Modules\Tax\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRateTaxRule extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [ "tax_rate_id", "tax_rule_id" ];

    public function tax_rate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function tax_rule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class);
    }
}
