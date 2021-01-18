<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\SlugAble;

class AttributeGroup extends Model
{
    use SlugAble;
    protected $fillable = ['name', 'position', 'is_user_defined' ,'slug','attribute_family_id'];
    public $timestamps = false;

}
