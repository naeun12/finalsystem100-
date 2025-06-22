<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class imagesDormImages extends Model
{
    protected $table = 'dorm_images';
    protected $primaryKey = 'images_id';

    protected $fillable = [
        'images_id',
        'dormitory_id',
        'main_image',
        'secondary_image',
        'third_image',
    ];
    public function dorm()
    {
        return $this->belongsTo(landlordDormManagement::class, 'dormitory_id', 'dorm_id');
    }
    

}
