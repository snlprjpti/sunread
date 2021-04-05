<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class AttributeGroup extends Model
{
    use Sluggable;
    protected $fillable = ['name', 'position', 'is_user_defined' ,'slug','attribute_family_id'];
    public $timestamps = false;
    public static  $SEARCHABLE = ['name','slug'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

}
