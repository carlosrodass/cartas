<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Card;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users'; //Especificacion tabla correspondiente al modelo


    /**
     * sell cards for User.
     */
    public function sell()
    {
        return $this->belongsTo(User::class); 
    }

}
