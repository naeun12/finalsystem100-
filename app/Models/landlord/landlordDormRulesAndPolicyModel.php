<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class landlordDormRulesAndPolicyModel extends Model
{
    protected $table = 'rules_and_policy_dorm';

    protected $fillable = [
        'id',
        'fkdorm_id',
        'fkrules_id',
        
    ];
}
