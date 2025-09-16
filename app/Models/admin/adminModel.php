<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class adminModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 
         protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    public $incrementing = true;
    protected $fillable = [
        'name',
        'username',
        'password',
    ];
     protected $hidden = [
        'password',
        'remember_token',
    ];

}
