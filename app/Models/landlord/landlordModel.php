<?php

namespace App\Models\landlord;
use Laravel\Sanctum\HasApiTokens;  

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\notificationModel; // ✅ Import the correct model

use Illuminate\Database\Eloquent\Factories\HasFactory;

class landlordModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 
    protected $guard = 'landlord';
    protected $table = 'landlords';
    protected $primaryKey = 'landlordID';
    public $timestamps = true;
    public $incrementing = false;  
    protected $keyType = 'string'; 
    protected $fillable = [
        'landlordID',
        'firstname',
        'lastname',
        'password',
        'email',
        'phoneNumber',
        'gender',
        'profilePicUrl',
        'govermentID',
        'businessPermit',
        'role',
        'is_deactivated',
        'isVerified'

    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    protected $hidden = [
        'password', // hide your password field properly
        'remember_token',
    ];
//     public function sentNotifications()
// {
//     return $this->morphMany(Notification::class, 'sender');
// }

// public function receivedNotifications()
// {
//     return $this->morphMany(Notification::class, 'receiver');
// }
  public function sentNotifications()
    {
        return $this->morphMany(notificationModel::class, 'sender', 'senderType', 'senderID');
    }

    public function receivedNotifications()
    {
        return $this->morphMany(notificationModel::class, 'receiver', 'receiverType', 'receiverID');
    }


}
