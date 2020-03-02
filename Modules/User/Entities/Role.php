<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\SlugAble;

class Role extends Model
{
    use SlugAble;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'permission_type', 'permissions','slug'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    /**
     * Get the admins.
     */
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public static function rules ($id = 0, $merge=[]) {
        return array_merge([
            'name' => 'required',
            'permission_type' => 'required',
            'permissions' => 'nullable'
        ], $merge);

    }

}

