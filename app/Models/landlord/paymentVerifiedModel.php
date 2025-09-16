<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class paymentVerifiedModel extends Model
{
    protected $table  = 'landlordVerifiedPayment';
    protected $primaryKey = 'paymentID';
    protected $fillable = ['fklandlordID', 
    'email', 
    'amount', 
    'paymongo_id', 
    'status', 
    'paymentMethod', 
    'referenceNumber', 
    'responsePayload'];

}
