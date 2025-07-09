<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class approvetenantsModel extends Model
{
    use HasFactory;
    protected $table = 'approved_tenants';
    protected $primaryKey = 'approvedID';
    public $incrementing = true;
    protected $fillable = [
        'fkroomID',
        'firstname',
        'lastname',
        'contactNumber',
        'contactEmail',
        'age',
        'gender',
        'move-in-date',
        'move-out-date',
        'paymentType',
        'paymentImage',
        'studentpictureId'
    ];


}
