<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;

class AttributeOptionTranslation extends Model
{
    protected $fillable = ['locale','name','attribute_option_id'];
    public $timestamps = false;
}
