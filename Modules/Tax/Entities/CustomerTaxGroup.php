<?php

namespace Modules\Tax\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Customer\Entities\CustomerGroup;

class CustomerTaxGroup extends Model
{
    use HasFactory;

    public static $SEARCHABLE = [ "name", "description" ];
    protected $fillable = [ "name", "description" ];

    public function tax_rules(): BelongsToMany
    {
        return $this->belongsToMany(TaxRule::class);
    }

    public function tax_rule_count(object $deleted): int
    {
        $tax_rule = DB::table("customer_tax_group_tax_rule")->where("customer_tax_group_id", $deleted->id)->count();
        return $tax_rule;
    }

    public function customer_group_count(object $deleted): int
    {
        $customer_group = CustomerGroup::whereCustomerTaxGroupId($deleted->id)->count();
        return $customer_group;
    }
}
