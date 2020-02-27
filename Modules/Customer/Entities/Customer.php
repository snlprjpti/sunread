<?php

namespace Modules\Customer\Entities;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Modules\Customer\Notifications\CustomerResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements  JWTSubject
{
    use Notifiable;

    protected $table = 'customers';

    protected $fillable = ['first_name', 'last_name', 'gender', 'date_of_birth', 'email', 'phone', 'password', 'api_token', 'customer_group_id', 'subscribed_to_news_letter', 'is_verified',  'status'];

    protected $hidden = ['password', 'api_token', 'remember_token'];
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
    }


    /**
     * Get the customer full name.
     */
    public function getNameAttribute() {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * Get the customer group that owns the customer.
     */
    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPassword($token));
    }


}
