<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'age',
        'gender',
        'status',
        'paymentType',
        'paymentImage',
        'studentpictureId'
    ];
   

    public function room()
    {
        return $this->belongsTo(roomModel::class, 'fkroomID', 'roomID');
    }

    public function tenant()
    {
        return $this->belongsTo(tenantModel::class, 'fktenantID', 'tenantID');
    }
}
