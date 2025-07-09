<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class dormrulesModel extends Model
{
    protected $table = 'rulesandpolicydorm';

    protected $fillable = [
        'id',
        'fkdormID',
        'fkruleID',
        
    ];
}
