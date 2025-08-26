<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\landlord\imageRoomsModel; // Assuming you have a Dorm model for dorm management.
use App\Models\landlord\roomModel; // Assuming you have a Dorm model for dorm management
use App\Models\landlord\landlordModel; // Assuming you have a Dorm model for dorm management



class dormModel extends Model
{
    use  HasFactory, Notifiable;  // Add HasApiTokens here
    protected $table = 'dorms';
    protected $primaryKey = 'dormID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'dormID',
        'dormName',
        'fklandlordID',
        'address',
        'totalRooms',
        'latitude',
        'longitude',
        'description',
        'contactEmail',
        'views',
        'contactPhone',
        'availability',
        'gcashNumber',
        'occupancyType',
        'buildingType',
        'created_at',
        
    ];
    public function amenities()
{
    return $this->belongsToMany(
        aminitiesModel::class,      // related model
        'amenitydorm',              // pivot table
        'fkdormID',                 // this model’s foreign key on pivot
        'fkaminityID'               // related model’s foreign key on pivot
    )->withPivot('id'); // optional, so you can still use pivot.id
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
        rulesModel::class,
        'rulesandpolicydorm',
        'fkdormID',
        'fkruleID',

    )->withPivot('id');
}
public function images()
{
    return $this->hasOne(dormimagesModel::class, 'fkdormID', 'dormID');
}
public function rooms()
{
    return $this->hasMany(roomModel::class, 'fkdormID', 'dormID');
}

public function landlord()
{
    return $this->belongsTo(landlordModel::class, 'fklandlordID', 'landlordID');
}
public function reviews()
{
    return $this->hasMany(\App\Models\reviewandratingModel::class, 'fkdormID', 'dormID');
}

}






