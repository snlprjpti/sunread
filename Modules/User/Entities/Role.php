<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class Role extends Model
{
    use Sluggable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'permission_type', 'permissions','slug'
    ];

    public static $SEARCHABLE = ['name' ,'description', 'permissions', 'permission_type', 'slug'];

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


}

