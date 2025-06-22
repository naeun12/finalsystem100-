<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class landlordAmintiesModel extends Model
{
    public $table = 'amenities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',    
        'created_at',
        'updated_at'
    ];
    public function dorms()
    {
        return $this->belongsToMany(
            landlordDormManagement::class,
            'amenity_dorm', // pivot table name
            'amenity_id',   // foreign key on pivot for this model
            'dorm_id'       // related key on pivot
        );
    }
    
    public function dormAmenities()
{
    return $this->hasMany(landlordDormAnimitiesModel::class, 'amenity_id');
}

    
}
