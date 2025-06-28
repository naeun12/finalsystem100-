<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class landlordRulesAndPolicyModel extends Model
{
    protected $table = 'rules_and_policies';

    protected $fillable = [
        'id',
        'rules_name',
        'created_at',
        'updated_at',
    ];
    public function dorms()
{
    return $this->belongsToMany(
        \App\Models\landlord\landlordDormManagement::class,
        'rules_and_policy_dorm',
        'fkrules_id',
        'fkdorm_id'
    );
}
public function dormRules()
{
    return $this->hasMany(landlordDormRulesModel::class, 'rules_id');

}
}
