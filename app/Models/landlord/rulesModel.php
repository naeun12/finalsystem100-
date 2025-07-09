<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class rulesModel extends Model
{
    protected $table = 'rulesandpolicies';

    protected $fillable = [
        'id',
        'rulesName',
        'created_at',
        'updated_at',
    ];
    public function dorms()
{
    return $this->belongsToMany(
        dormModel::class,
        'rulesandpolicydorm',
        'fkruleID',
        'fkdormID'
    );
}
public function dormRules()
{
    return $this->hasMany(rulesModel::class, 'fkruleID');

}
}
