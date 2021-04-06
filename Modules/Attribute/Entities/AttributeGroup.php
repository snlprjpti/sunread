<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class AttributeGroup extends Model
{
    use Sluggable;

    public static  $SEARCHABLE = [ "name", "slug" ];
    public $timestamps = false;
    protected $fillable = [ "name", "position", "is_user_defined" ,"slug", "attribute_family_id" ];

    /**
     * Attributes relationship
     * 
     * @return Attribute
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public function attribute_family()
    {
        return $this->belongsTo(AttributeFamily::class);
    }
}
