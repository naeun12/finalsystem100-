<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\conversationModel; // 👈 import
use App\Models\tenant\messageModel as Message; // 👈 import self
use App\Models\tenant\tenantaccountModel;
use App\Models\landlord\landlordAccountModel;

class messageModel extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_role',
        'receiver_id',
        'receiver_role',
        'message',
        'reply_to_id',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Dynamic accessor for sender model
     */
    public function getActualSenderAttribute()
    {
        switch ($this->sender_role) {
            case 'tenant':
                return tenantaccountModel::find($this->sender_id);
            case 'landlord':
                return landlordAccountModel::find($this->sender_id);
            default:
                return null;
        }
    }

    public function conversation()
    {
        return $this->belongsTo(conversationModel::class, 'conversation_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }
}
