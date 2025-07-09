<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class featuresModel extends Model
{
    public $table = 'roomfeatures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'featureName',
        'created_at',
        'updated_at'
    ];
}
