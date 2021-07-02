<?php

namespace Modules\Tax\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class TaxRule extends Model
{
    use HasFactory;

    protected $fillable = [ "customer_group_class", "product_taxable_class", "name", "position", "priority", "subtotal", "status" ];
}
