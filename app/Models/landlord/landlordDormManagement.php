<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\landlord\imageRoomsModel; // Assuming you have a Dorm model for dorm management.
use App\Models\landlord\landlordRoomModel; // Assuming you have a Dorm model for dorm management
use App\Models\landlord\landlordAccountModel; // Assuming you have a Dorm model for dorm management



class landlordDormManagement extends Model
{
    use  HasFactory, Notifiable;  // Add HasApiTokens here
    protected $table = 'dorms';
    protected $primaryKey = 'dorm_id';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'dorm_id',
        'dorm_name',
        'landlord_id',
        'address',
        'latitude',
        'longitude',
        'description',
        'amenities',
        'contact_email',
        'contact_phone',
        'availability',
        'occupancy_type',
        'room_features',
        'building_type',
        'rules',
        'created_at',
        
    ];
    public function amenities()
{
    return $this->belongsToMany(
        \App\Models\landlord\landlordAmintiesModel::class,
        'amenity_dorm',
        'dorm_id',
        'amenity_id',
        
    )->withPivot('id');
    
}
protected static function booted()
{
    static::deleting(function ($dorm) {
        // Detach amenities first (pivot table)
        $dorm->amenities()->detach();

        // Delete each room one by one to trigger Room model deleting events
        $dorm->rooms->each(function ($room) {
            $room->delete();
        });
    });
}
public function rulesAndPolicy()
{
    return $this->belongsToMany(
        \App\Models\landlord\landlordRulesAndPolicyModel::class,
        'rules_and_policy_dorm',
        'fkdorm_id',
        'fkrules_id',

    )->withPivot('id');
}
public function images()
{
    return $this->hasOne(imagesDormImages::class, 'dormitory_id', 'dorm_id');
}
public function rooms()
{
    return $this->hasMany(landlordRoomModel::class, 'dormitory_id', 'dorm_id');
}

public function landlord()
{
    return $this->belongsTo(landlordAccountModel::class, 'landlord_id', 'landlord_id');
}
public function totalCapacity()
{
    return $this->rooms()->sum('capacity');
}



}






