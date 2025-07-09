<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\conversationModel; // 👈 import
use App\Models\tenant\messageModel as Message; // 👈 import self
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;

class messageModel extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'conversationID',
        'senderID',
        'senderRole',
        'receiverID',
        'receiverRole',
        'message',
        'replyToId',
        'isRead',
        'sentAt',
    ];

    protected $casts = [
        'isRead' => 'boolean',
        'sentAt' => 'datetime',
    ];

    /**
     * Dynamic accessor for sender model
     */
    public function getActualSenderAttribute()
    {
        switch ($this->senderRole) {
            case 'tenant':
                return tenantModel::find($this->senderID);
            case 'landlord':
                return landlordAccountModel::find($this->senderID);
            default:
                return null;
        }
    }

    public function conversation()
    {
        return $this->belongsTo(conversationModel::class, 'conversationID');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'replyToId');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'replyToId');
    }

}
