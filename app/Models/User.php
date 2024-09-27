<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable;



    protected $table = 'users';
    
    protected $fillable = ['email', 'password', 'name', 'number', 'type', 'id_token'];
    

    // static public function User(){
    //     return User::get();
    // }


}
