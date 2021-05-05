<?php

namespace Modules\Customer\Entities;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Core\Traits\HasFactory;
use Modules\Customer\Notifications\CustomerResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\Core\Traits\HasFactory;

class Customer extends Authenticatable implements  JWTSubject
{
    use Notifiable, HasFactory;

    public static $SEARCHABLE =  [ "first_name", "last_name", "email" ];
    protected $fillable = [ "first_name", "last_name", "gender", "date_of_birth", "email", "phone", "password", "api_token", "customer_group_id", "subscribed_to_news_letter", "is_verified",  "status" ];
    protected $hidden = [ "password", "api_token", "remember_token" ];

    public function getJWTIdentifier(): ?string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getNameAttribute(): ?string
    {
        return ucwords("{$this->first_name} {$this->last_name}");
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, "customer_group_id");
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class ,"customer_id");
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPassword($token));
    }
}
