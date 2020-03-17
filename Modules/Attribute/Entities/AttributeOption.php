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

    public function createUpdateOptionTranslation(Array $optionInputs)
    {
        if(isset($optionInputs['translations'])){
            $translated_options = $optionInputs['translations'];
            foreach ($translated_options as $translated_option){
                $check_attributes = ['locale' => $translated_option['locale'], 'attribute_option_id' => $this->id];
                $optionInputs = array_merge($translated_option, $check_attributes);
                $option_translation = AttributeOptionTranslation::firstOrNew($check_attributes);
                $option_translation->fill($optionInputs);
                $option_translation->save();

            }

        }
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
