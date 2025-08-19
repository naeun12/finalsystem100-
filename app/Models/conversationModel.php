<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use  App\Models\messageModel;


class conversationModel extends Model
{
    use HasFactory;
    protected $table = 'conversations'; // 👈 FIXED!


    protected $fillable = [
        'initiatorID',
        'initiatorRole',
        'topic',
    ];
    protected $casts = [
        'initiatorID' => 'string',
    ];

    /**
     * Get all messages in this conversation
     */
    public function messages()
{
    return $this->hasMany(messageModel::class, 'conversationID');
}

}
