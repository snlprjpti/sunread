<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{

    protected $fillable = ['name','position', 'attribute_id'];

    /**
     * Get the attribute that owns the attribute option.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }


}
