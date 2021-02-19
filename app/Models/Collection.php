<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Card;

class Collection extends Model
{
    use HasFactory;

    protected $table = 'collections'; //Especificacion tabla correspondiente al modelo


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_name',
        'image',
        'publish_date',
    ];


}
