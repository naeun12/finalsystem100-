<?php

namespace App\Models\landlord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status',
        'studentpictureId'
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
}
