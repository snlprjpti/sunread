<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    public $timestamps = false;
    protected $fillable = [ "name", "position", "attribute_id" ];

    /**
     * Attribute relationship
     * 
     * @return Attribute
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
    
    /**
     * Translations relationship
     * 
     * @return AttributeOptionTranslation
     */
    public function translations()
    {
        return $this->hasMany(AttributeOptionTranslation::class, "attribute_option_id");
    }

    /**
     * Get Translated data
     * 
     * @return AttributeOptionTranslation
     */
    public function translate($locale)
    {
        return $this->translations()->where("locale", $locale)->first();
    }
}
