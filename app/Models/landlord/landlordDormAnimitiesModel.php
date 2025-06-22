<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class landlordDormAnimitiesModel extends Model
{
    protected $table = 'amenity_dorm';
    protected $primaryKey = 'id';
    public $timestamps = false; // Assuming you don't have created_at and updated_at fields

    protected $fillable = [
        'id',
        'dorm_id',
        'amenity_id',
        'created_at',
        'updated_at'
    ];

    public function amenity()
{
    
    return $this->belongsTo(landlordAmintiesModel::class, 'amenity_id');
}
}
