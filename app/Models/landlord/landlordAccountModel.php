<?php

namespace App\Models\landlord;
use Laravel\Sanctum\HasApiTokens;  // Import Sanctum trait

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class landlordAccountModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;  // Add HasApiTokens here
    protected $guard = 'landlord';

    protected $table = 'landlords';
    protected $primaryKey = 'landlord_id';
    public $timestamps = true;
    public $incrementing = false;  // or false if your PK is not auto-incrementing

    protected $keyType = 'string'; // or 'string' if your PK is string

    protected $fillable = [
        'landlord_id',
        'firstname',
        'lastname',
        'password_hash',
        'email',
        'phonenumber',
        'gender',
        'profile_pic_url',
        'goverment_id',
        'business_permit',
        'verify_account',
        'role',
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

}
