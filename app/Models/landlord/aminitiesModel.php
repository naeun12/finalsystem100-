<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class aminitiesModel extends Model
{
    public $table = 'amenities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'aminityName',    
        'created_at',
        'updated_at'
    ];
    public function dorms()
    {
        return $this->belongsToMany(
            dormModel::class,
            'amenitydorm', // pivot table name
            'fkaminityID',   // foreign key on pivot for this model
            'fkdormID'       // related key on pivot
        );
    }
    
    public function dormAmenities()
{
    return $this->hasMany(dormaminitiesModel::class, 'amenity_id');
}

    
}
