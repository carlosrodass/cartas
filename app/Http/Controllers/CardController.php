<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;

class CardController extends Controller
{
    public function createCard(Request $request){
       //comprobar que el usuario tenga role Admin

       //pedir nombre, decripcion y coleccion
       $card = Card::create([
        'card_name' => $request->get('card_name'),
        'description' => $request->get('description'),
        'collection' => $request->get('collection'),
        
    ]);

       

       //save carta
    }


    public function updateCard(){

    }
}
