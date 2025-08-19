<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class approvepaymentModel extends Model
{
      use HasFactory;
    protected $table = 'approvepayments';
    protected $primaryKey = 'paymentID';
    public $incrementing = true;

    protected $fillable = [
        'paymentID',
        'fkapprovedID',
        'paymentType',
        'paymentImage',
        'amount',
        'status'
    ];
    public function approvedTenant()
    {
        return $this->belongsTo(approvetenantsModel::class, 'fkapprovedID', 'approvedID');
    }
 

    
}
