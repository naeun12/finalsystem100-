<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class landlordRoomFeaturesModel extends Model
{
    public $table = 'room_features';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'feature_name',
        'created_at',
        'updated_at'
    ];
}
