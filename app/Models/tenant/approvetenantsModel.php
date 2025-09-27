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
        'source_type',
         'source_id',
         'notifyRent',
         'extension_decision',
        'age',
        'gender',
        'moveInDate',
        'moveOutDate',
        'notify-extend',
        'paymentType',
        'deleted_at',
        'isDeleted',
        'paymentImage',
        'pictureID',
        'has_rated',
        'status',
        'paymentOption',
        'extension_payment_status',
        'extensionDate',
    ];
     public function room()
    {
        return $this->belongsTo(roomModel::class, 'fkroomID', 'roomID');
    }
    public function payments()
{
    return $this->hasMany(approvepaymentModel::class, 'fkapprovedID', 'approvedID');
}


  


}
