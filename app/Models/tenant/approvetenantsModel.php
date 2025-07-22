<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\tenant\tenantModel;
use App\Models\landlord\roomModel;

class approvetenantsModel extends Model
{
    use HasFactory;
    protected $table = 'approved_tenants';
    protected $primaryKey = 'approvedID';
    public $incrementing = true;
    protected $fillable = [
        'approvedID',
        'fktenantID',
        'fkroomID',
        'firstname',
        'lastname',
        'contactNumber',
        'contactEmail',
        'age',
        'gender',
        'moveInDate',
        'moveOutDate',
        'paymentType',
        'paymentImage',
        'studentpictureId'
    ];
     public function room()
    {
        return $this->belongsTo(roomModel::class, 'fkroomID', 'roomID');
    }

  


}
