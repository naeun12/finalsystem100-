<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\landlord\landlordDormManagement; // Assuming you have a Dorm model for dorm management

class landlordRoomModel extends Model
{
    use  HasFactory, Notifiable;  // Add HasApiTokens here
    protected $table = 'rooms';
    protected $primaryKey = 'room_id';
    public $timestamps = true;
  

    protected $fillable = [
        'room_id',
        'dormitory_id',
        'landlord_id',
        'room_number',
        'room_type',
        'availability',
        'price',
        'furnishing_status',
        'listing_type',
        'area_sqm',
        'gender_preference',
        'room_images',
        'capacity',
        'created_at',
        
    ];
    public function dorm()
{
    return $this->belongsTo(landlordDormManagement::class, 'dormitory_id', 'dorm_id');
}
public function features()
{
    return $this->belongsToMany(
        'App\Models\landlord\landlordRoomFeaturesModel', // Adjust the namespace as needed
        'room_features_rooms', // Pivot table name
        'fkroom_id', // Foreign key in the pivot table
        'fkfeature_id' // Foreign key in the pivot table
    )->withPivot('fkroom_id', 'fkfeature_id'); // Include pivot timestamps if needed
}

}

