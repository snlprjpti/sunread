<?php

namespace Modules\User\Entities;

use Modules\Core\Traits\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Modules\User\Notifications\ResetPasswordNotification;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    public static $SEARCHABLE = [ "first_name", "email" ];
    protected $fillable = [ "first_name", "last_name", "email", "password", "api_token", "role_id", "status", "company", "address", "profile_image" ];
    protected $hidden = [ "password", "api_token", "remember_token" ];
    protected $appends = [ "avatar", "profile_image_url" ];

    /**
     * Get the role that owns the admin.
     * 
     * @return Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Checks if admin has permission to perform certain action.
     *
     * @param string $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if ($this->role->permission_type == 'custom' && ! $this->role->permissions) return false;

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

    /**
     * Send password reset notification
     * 
     * @param string $token
     * @return ResetPasswordNotification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if the user has role
     * 
     * @param string $roleSlug
     * @return boolean
     */
    public function hasRole($roleSlug): bool
    {
        if (empty($roleSlug) || empty($this->role)) return false;
        if ($this->role->slug == $roleSlug) return true;

        return false;
    }

    /**
     * Get Avatar image url
     * 
     * @return string
     */
    public function getAvatarAttribute()
    {
        return $this->getImage();
    }

    /**
     * Get Profile image url
     * 
     * @return string
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? Storage::url($this->profile_image) : null;
    }

    /**
     * Generate URL for image_type
     * 
     * @param string $image_type
     * @return string
     */
    public function getImage($image_type = "main_image")
    {
        if ( !$this->profile_image ) return null;
        $image_url = null;

        switch ($image_type){
            case 'main_image':
                $image_url = $this->getDimensionPath("user_image.image_dimensions.user.main_image");
                break;

            case 'gallery_image':
                $image_url = $this->getDimensionPath("user_image.image_dimensions.user.gallery_images");
                break;
        }

        return $image_url;
    }

    /**
     * Get the path from dimension and type
     * 
     * @param string $config
     * @param string $folder
     * @return string
     */
    private function getDimensionPath($config, $folder = "main")
    {
        $dimension = config($config)[0];
        $width = $dimension["width"];
        $height = $dimension["height"];

        $file_array = $this->getFileNameArray();
        return Storage::url("{$file_array['folder']}/{$folder}/{$width}x{$height}/{$file_array['file']}");
    }

    /**
     * Get array of folder and filename from profile_image
     * 
     * @return array
     */
    private function getFileNameArray()
    {
        $path_array = explode("/", $this->profile_image);
        $file_name = $path_array[count($path_array) - 1];
        unset($path_array[count($path_array) - 1]);

        return [
            "folder" => implode("/", $path_array),
            "file" => $file_name
        ];
    }

    /**
     * Get full name
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return ucwords("{$this->first_name} {$this->last_name}");
    }

}
