<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;

class CardController extends Controller
{
    public function createCard(Request $request){
        //, $token
    
    $response= "";
    
    //Recibiendo informacion dada por el usuario
    $data = $request->getContent();
    //Decodificando el json en String
    $data = json_decode($data);
    
    //Si existend datos
    if($data){

        $card = new Card();
        //Obteniendo la coleccion que tenga el mismo nombre dado por el usuario en el campo coleccion
        $collection = Collection::where('collection_name', $data->collection)->get()->first();
        // $user = User::where('password',$token)->get()->first();
        $user = User::where('id', 1)->get()->first();
        //Si la coleccion existe
        if($collection){

            //Relleno los datos de la carta
            $card->card_name = $data->card_name; //card_name
            $card->description = $data-> description; //Description
            $card->collection = $data-> collection; //Collection
            $card->user_id = $user->id; //Id del usuario creador

            try{
                $card->save();
                $card_id=$card->id;
                $response="OK";
            } catch(\Exception $e){
                $response=$e->getMessage();
            }

            //Rellenando la tabla intermedia
            $cardCollection = new cardCollection();
            $cardCollection->card_id =$card_id;
            $cardCollection->collection_id =  $collection->id;
            $cardCollection->save();
        }
        //Si no existe la coleccion
        else{
            $collection= new Collection();
            $collection->name = $data->collection;
            $card->name = $data->card_name;
            $card->description = $data->description;
            $card->collection=$data->collection;
            $card->user_id=$user->id;
            try{
                $card->save();
                $collection->save();
                $card_id=$card->id;
                $collection_id=$collection->id;
                $response="Succes";
            } catch(\Exception $e){
                $response=$e->getMessage();
            }
            $cardCollection = new cardCollection();
            $cardCollection->card_id =1;
            $cardCollection->collection_id = $collection->id; 
            $cardCollection->save(); 
        }

       

    //Si no existen datos
    }else{
        $response="Data empty";
    }

    return $response;
  
    }


    public function updateCard(){

    }
}
