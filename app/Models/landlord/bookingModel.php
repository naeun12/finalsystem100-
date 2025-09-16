<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\tenant\bookingpaymentModel;
use App\Models\tenant\tenantModel;


class bookingModel extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $primaryKey = 'bookingID';
    public $incrementing = true;
    protected $fillable = [
        'fkroomID',
        'fktenantID',
        'firstname',
        'lastname',
        'contactNumber',
        'contactEmail',
        'moveInDate',
        'moveOutDate',
        'age',
        'gender',
        'status',
        'pictureID',
        'isDeleted',
    ];
   

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
    return $this->hasMany(\App\Models\tenant\bookingpaymentModel::class, 'fkbookingID', 'bookingID');
}




}
