<?php

namespace Modules\Attribute\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\SlugAble;

class AttributeFamily extends Model
{
    use SlugAble;
    protected $fillable = ['name', 'slug'];
    public $timestamps= false;
    public static $SEARCHABLE = ['name'];
    public function attributeGroups()
    {
        return $this->hasMany(AttributeGroup::class, 'attribute_family_id');
    }

    public function custom_attributes()
    {
        return DB::table('attributes')
            ->join('attribute_groups', 'attributes.attribute_group_id', '=', 'attribute_groups.id')
            ->join('attribute_families', 'attribute_groups.attribute_family_id', '=', 'attribute_families.id')
            ->where('attribute_families.id', $this->id)
            ->select('attributes.*');
    }
}
