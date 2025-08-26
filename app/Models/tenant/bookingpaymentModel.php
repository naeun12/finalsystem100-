<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\tenant\payment;
use App\Models\landlord\bookingModel;



class bookingpaymentModel extends Model
{
     use HasFactory;

    protected $table = 'bookingpayments';
    protected $primaryKey = 'paymentID';
    protected $fillable = ['fkbookingID', 'paymentType', 'amount','paymentImage'];

    public function booking()
    {
        return $this->belongsTo(bookingModel::class, 'fkbookingID', 'bookingID');
    }
}
