<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reviewandratingModel extends Model
{
    protected $table = 'dorm_reviews';
    protected $primaryKey = 'reviewID';
    public $timestamps = true;
     protected $fillable = [
        'fkdormID',
        'fkapprovedID',
        'rating',
        'review',
    ];
        public function dorm()
     {
        return $this->belongsTo(\App\Models\landlord\dormModel::class, 'fkdormID', 'dormID');
    }
public function tenant()
{
    return $this->belongsTo(\App\Models\tenant\approvetenantsModel::class, 'fkapprovedID', 'approvedID');
}


    public function getRatingPercentageAttribute()
    {
        return $this->rating ? round(($this->rating / 5) * 100, 2) : 0;
    }

}
