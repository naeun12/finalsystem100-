<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class conversationModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'initiator_role',
        'topic',
    ];

    /**
     * Get all messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
