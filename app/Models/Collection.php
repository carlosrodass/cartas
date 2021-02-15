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


    /**
     * Get the User for cards.
     */
    public function users()
    {
        return $this->belongsTo(User::class); //One to Many inverse
    }


     /**
     * get cards for collections.
     */
    public function cards()
    {
        return $this->belongsToMany(Card::class); //Many to Many
    }
}
