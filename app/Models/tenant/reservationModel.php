<?php

namespace App\Models\tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\tenant\tenantModel;

use App\Models\landlord\dormModel;
use App\Models\landlord\roomModel;


class reservationModel extends Model
{
    use HasFactory;
    protected $table = 'reservation';
    protected $primaryKey = 'reservationID';
    public $incrementing = true;
    protected $fillable = [
        'fkdormitoryID',
        'fkroomID',
        'fktenantID',
        'firstname',
        'lastname',
        'contactNumber',
        'contactEmail',
        'age',
        'gender',
        'moveInDate',
        'status',
        'pictureID',
        'isDeleted',
    ];
    public function dormitory()
    {
        return $this->belongsTo(dormModel::class, 'fkdormitoryID', 'dormID');
    }

    public function room()
    {
        return $this->belongsTo(roomModel::class, 'fkroomID', 'roomID');
    }
    

    public function tenant()
    {
        return $this->belongsTo(tenantModel::class, 'fktenantID', 'tenantID');
    }
    public function payment()
{
    return $this->hasOne(reservationpaymentModel::class, 'reservationID', 'reservationID');
}

}
