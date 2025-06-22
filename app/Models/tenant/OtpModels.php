<?php

namespace App\Models\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtpModels extends Model
{
      
    use HasFactory, SoftDeletes;

    protected $table = 'otp'; 
    protected $primaryKey = 'otp_id'; 
    public $timestamps = true; 
    protected $fillable = [
        'email',
        'otpCode',
        'otpExpires_at',
        'role'

    ];
        protected $dates = ['otpExpires_at', 'deleted_at'];



}
