<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class roomfeaturesModel extends Model
{
    protected $table = 'room_features_rooms'; // Assuming this is the pivot table name
    protected $primaryKey = 'id';
    public $timestamps = false; // Assuming you don't have created_at and updated_at fields

    protected $fillable = [
        'id',
        'fkroomID',
        'fkfeatureID',
        'created_at',
        'updated_at'
    ];
}
