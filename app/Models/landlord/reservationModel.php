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
        'contact_number',
        'contact_email',
        'age',
        'gender',
        'status',
        'payment_type',
        'payment_image',
        'studentpicture_id'
    ];
    public function dormitory()
    {
        return $this->belongsTo(Dorm::class, 'fkdormitoryID', 'dorm_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'fkroomID', 'room_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'fktenantID', 'tenant_id');
    }
}
