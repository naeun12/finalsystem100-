<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class otpModel extends Model
{
      
    use HasFactory, SoftDeletes;

    protected $table = 'otp'; 
    protected $primaryKey = 'otpID'; 
    public $timestamps = true; 
    protected $fillable = [
        'email',
        'otpCode',
        'otpExpiresAt',
        'role'

    ];
        protected $dates = ['otpExpiresAt', 'deleted_at'];



}
