<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class dormaminitiesModel extends Model
{
    protected $table = 'amenitydorm';
    protected $primaryKey = 'id';
    public $timestamps = false; // Assuming you don't have created_at and updated_at fields

    protected $fillable = [
        'id',
        'fkdormID',
        'fkaminityID',
        'created_at',
        'updated_at'
    ];

    public function amenity()
{
    
    return $this->belongsTo(aminitiesModel::class, 'amenity_id'); 
}
}
