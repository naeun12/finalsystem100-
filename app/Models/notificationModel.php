<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class notificationModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Polymorphic relationship for the sender (Landlord or Tenant)
    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }
    public function receiver()
    {
        return $this->morphTo(__FUNCTION__, 'receiver_type', 'receiver_id');
    }

}
