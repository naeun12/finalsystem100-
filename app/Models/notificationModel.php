<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;

class notificationModel extends Model
{
    use HasFactory;
    protected $table = 'notifications'; // ✅ correct table name

    protected $fillable = [
        'senderID',
        'senderType',
        'receiverID',
        'receiverType',
        'title',
        'message',
        'isRead',
        'readAt',
    ];

    protected $casts = [
        'isRead' => 'boolean',
        'readAt' => 'datetime',
    ];

    // Polymorphic relationship for the sender (Landlord or Tenant)
    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'senderType', 'senderID');
    }
    public function receiver()
    {
        return $this->morphTo(__FUNCTION__, 'receiverType', 'receiverID');
    }


}
