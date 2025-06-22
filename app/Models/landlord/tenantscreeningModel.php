<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class tenantscreeningModel extends Model
{
    protected $table = 'tenant_screening';
    protected $primaryKey = 'tenantscreening_id';
    public $timestamps = true;

    protected $fillable = [
        'fkdormitory_id',
        'landlord_id',
        'fkroom_id',
        'fktenant_id',
        'firstname',
        'lastname',
        'contact_number',
        'contact_email',
        'age',
        'gender',
        'status',
        'studentpicture_id',
        'payment_type',
        'payment_image',
        'created_at',
        
    ];
    public function room()
{
    return $this->belongsTo(landlordRoomModel::class, 'fkroom_id', 'room_id');
}

public function dorm()
{
    return $this->belongsTo(landlordDormManagement::class, 'fkdormitory_id', 'dorm_id');
}

public function tenant()
{
    return $this->belongsTo(\App\Models\TenantModel::class, 'fktenant_id', 'tenant_id');
}

public function landlord()
{
    return $this->belongsTo(landlordAccountModel::class, 'landlord_id', 'landlord_id');
}
}
