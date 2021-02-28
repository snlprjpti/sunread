<?php

namespace Modules\User\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\User\Notifications\ResetPasswordNotification;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    public static  $SEARCHABLE = ['name', 'email'];
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'email', 'password', 'api_token', 'role_id', 'status',"first_name", "last_name", "company", "address","profile_image"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'remember_token',
    ];

    protected $appends = ['avatar'];
    /**
     * Get the role that owns the admin.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    /**
     * Checks if admin has permission to perform certain action.
     *
     * @param  String  $permission
     * @return Boolean
     */
    public function hasPermission($permission)
    {
        if ($this->role->permission_type == 'custom' && ! $this->role->permissions)
            return false;

        return in_array($permission, $this->role->permissions);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
        // TODO: Implement getJWTCustomClaims() method.
    }


    public static function rules ($id = 0, $merge=[]) {
        return array_merge([
            'name' => 'required',
            'email' => 'required|unique:admins,email,'.($id ? ",$id" : ''),
            'password' => 'nullable|confirmed',
            'status' => 'required|boolean',
            'role_id' => 'required|integer'
        ], $merge);


    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function hasRole($roleSlug): bool
    {
        if (empty($roleSlug) || empty($this->role)){
            return false;
        }

        if ($this->role->slug ==  $roleSlug) {
            return true;
        }

        return false;
    }

    public function getAvatarAttribute()
    {
        return $this->getImage();
    }

    public function getImage($image_type = 'main_image')
    {
        $main_image_dimension = config('user_image.image_dimensions.user.main_image')[0];
        $gallery_image_dimension = config('user_image.image_dimensions.user.gallery_images')[0];

        switch ($image_type){

            case 'main_image':
                $d = $main_image_dimension['width'] . '_' . $main_image_dimension['height'] . '_';

                if(isset($this->profile_image)){
                    return asset('storage/images/admin/'.$d.$this->profile_image);
                }
                break;

            case 'gallery_image':
                $d = $gallery_image_dimension['width'] . '_' . $gallery_image_dimension['height'] . '_';
                if(isset($this->profile_image)){
                    return asset('storage/images/admin/'.$d . $this->profile_image);
                }
                break;
        }
        return 1;

    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

}
