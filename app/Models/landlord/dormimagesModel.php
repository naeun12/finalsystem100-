<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class dormimagesModel extends Model
{
    protected $table = 'dormimages';
    protected $primaryKey = 'imagesID';

    protected $fillable = [
        'imagesId',
        'fkdormID',
        'mainImage',
        'secondaryImage',
        'thirdImage',
    ];
    public function dorm()
    {
        return $this->belongsTo(dormModel::class, 'fkdormID', 'dormID');
    }
    

}
