<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Collection;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards'; //Especificacion tabla correspondiente al modelo


      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_name',
        'description',
        'collection',
        'user_id'
    ];

     /**
     * Get the User for collections.
     */
    public function users()
    {
        return $this->belongsToMany(User::class); //One to Many inverse
    }


    /**
     * sell cards for User.
     */
    public function sell()
    {
        return $this->belongsToMany(User::class); //Many to Many
    }

    /**
     * get collection for cards.
     */
    public function collection()
    {
        return $this->belongsToMany(Collection::class); //Many to Many
    }
}
