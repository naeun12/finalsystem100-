<?php

namespace App\Models\tenant;
use Laravel\Sanctum\HasApiTokens;  // Import Sanctum trait
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;


class tenantaccountModel extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $guard = 'tenant';

    protected $table = 'tenants'; 

    protected $primaryKey = 'tenant_id'; 
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'tenant_id', 
        'firstname',
        'lastname',
        'password_hash',
        'email',
        'phonenumber',
        'gender',
        'city',
        'province',
        'region',
        'postalcode',
        'currentaddress',
        'profile_pic_url',
    ];
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected $hidden = [
        'password_hash', // hide your password field properly
        'remember_token',
    ];
    public function sentNotifications()
{
    return $this->morphMany(Notification::class, 'sender');
}

public function receivedNotifications()
{
    return $this->morphMany(Notification::class, 'receiver');
}


    // If you want to use this model for authentication
    // Make sure you extend Authenticatable instead of Model
}


