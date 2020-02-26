<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

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
            'email' => 'required|unique:admins,email'.($id ? ",$id" : ''),
            'password' => 'nullable|confirmed',
            'status' => 'required|boolean',
            'role_id' => 'required|integer'
        ], $merge);

    }
}

