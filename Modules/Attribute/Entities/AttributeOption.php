<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{

    public $timestamps = false;
    protected $fillable = ['name','position', 'attribute_id'];

    /**
     * Get the attribute that owns the attribute option.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }




    public function translations()
    {
        return $this->hasMany(AttributeOptionTranslation::class,'attribute_option_id');
    }

    public function translate($locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }


}
