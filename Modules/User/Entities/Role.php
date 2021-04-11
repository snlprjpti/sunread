<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;

class Role extends Model
{
    use Sluggable;

    public static $SEARCHABLE = [ "name", "description", "permissions", "permission_type", "slug" ];
    protected $fillable = [ "name", "description", "permission_type", "permissions", "slug" ];
    protected $casts = [ "permissions" => "array" ];

    /**
     * Admins relationship
     * 
     * @return Admin
     */
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
