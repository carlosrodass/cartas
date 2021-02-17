<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cardCollection extends Model
{
    use HasFactory;

    protected $table = 'cards_users'; //Especificacion tabla correspondiente al modelo
}
